<?php
require_once __DIR__ . '/../core.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["message" => "Usuário não autenticado."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (
    !isset($data->nome) || empty(trim($data->nome)) ||
    !isset($data->email) || empty(trim($data->email)) ||
    !isset($data->login) || empty(trim($data->login)) ||
    !isset($data->senha) || empty(trim($data->senha))
) {
    http_response_code(400);
    echo json_encode(["message" => "Nome, Email, Login e Senha são obrigatórios."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query_user = "INSERT INTO usuarios (nome, email, login, senha_hash, celular, ativo) 
               VALUES (:nome, :email, :login, :senha, :celular, :ativo) RETURNING id";
$query_papeis = "INSERT INTO usuarios_papeis (usuario_id, papel_id) VALUES (:usuario_id, :papel_id)";

try {
    $db->beginTransaction();
    
    $stmt_user = $db->prepare($query_user);

    $ativo = isset($data->ativo) ? (bool)$data->ativo : true;
    $celular = isset($data->celular) ? htmlspecialchars(strip_tags($data->celular)) : null;

    $stmt_user->bindParam(':nome', $data->nome);
    $stmt_user->bindParam(':email', $data->email);
    $stmt_user->bindParam(':login', $data->login);
    $stmt_user->bindParam(':senha', $data->senha);
    $stmt_user->bindParam(':celular', $celular);
    $stmt_user->bindParam(':ativo', $ativo, PDO::PARAM_BOOL);

    $stmt_user->execute();
    $usuario_id = $stmt_user->fetchColumn();

    if (isset($data->papeis) && is_array($data->papeis)) {
        $stmt_papeis = $db->prepare($query_papeis);
        foreach ($data->papeis as $papel_id) {
            $stmt_papeis->bindParam(':usuario_id', $usuario_id);
            $stmt_papeis->bindParam(':papel_id', $papel_id);
            $stmt_papeis->execute();
        }
    }
    
    $db->commit();
    http_response_code(201);
    echo json_encode(["message" => "Usuário criado com sucesso."]);

} catch (PDOException $e) {
    $db->rollBack();
    if ($e->getCode() == '23505') {
        http_response_code(409);
        echo json_encode(["message" => "Este Email ou Login já está em uso."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Não foi possível criar o usuário."]);
    }
}
?>