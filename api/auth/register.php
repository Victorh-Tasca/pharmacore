<?php
/**
 * Endpoint para Cadastro de Novos Usuários.
 *
 * Recebe dados de um novo usuário via POST, valida as informações,
 * e realiza a inserção no banco de dados.
 * A senha é hasheada automaticamente pelo trigger do banco de dados.
 */

require_once __DIR__ . '/../core.php';
require_once __DIR__ . '/../config/database.php';

// Responde a requisições pre-flight (OPTIONS) do CORS, essenciais para APIs.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Garante que apenas o método POST seja aceito neste endpoint.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

// Validação de presença e formato dos dados essenciais recebidos do frontend.
if (
    !isset($data->nome) || empty(trim($data->nome)) ||
    !isset($data->email) || !filter_var($data->email, FILTER_VALIDATE_EMAIL) ||
    !isset($data->senha) || empty(trim($data->senha))
) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Dados inválidos. Verifique os campos e tente novamente."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

// NOTA: O schema do banco de dados (schema_farmacia.sql) possui um trigger
// chamado `trg_hash_senha_usuarios` que intercepta a inserção e automaticamente
// cria um hash seguro para a senha usando `crypt()`. Por isso, enviamos a senha
// em texto plano para o `bindParam`, confiando que o DB fará o trabalho de hashing.
$query = "INSERT INTO usuarios (nome, email, login, senha_hash, ativo) VALUES (:nome, :email, :login, :senha, TRUE)";

$stmt = $db->prepare($query);

// Limpa os dados para prevenir ataques de XSS (Cross-Site Scripting).
$nome = htmlspecialchars(strip_tags($data->nome));
$email = htmlspecialchars(strip_tags($data->email));
$senha = htmlspecialchars(strip_tags($data->senha));

$stmt->bindParam(':nome', $nome);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':login', $email); // Usando e-mail como login por padrão
$stmt->bindParam(':senha', $senha);

try {
    $stmt->execute();
    http_response_code(201); // Created
    echo json_encode(["message" => "Conta criada com sucesso!"]);
} catch (PDOException $e) {    
    // Trata a violação de constraint UNIQUE (código 23505 no PostgreSQL),
    // que indica que o e-mail ou login já existem.
    if ($e->getCode() == '23505') {
        http_response_code(409); // Conflict
        echo json_encode(["message" => "Este e-mail já está em uso."]);
    } else {
        http_response_code(500); // Internal Server Error
        // Em ambiente de produção, é recomendado logar o erro em vez de exibi-lo.
        // error_log("Erro ao registrar usuário: " . $e->getMessage());
        echo json_encode(["message" => "Não foi possível criar a conta."]);
    }
}