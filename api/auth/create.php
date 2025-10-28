<?php
/**
 * Endpoint para CRIAR um novo medicamento.
 * Recebe os dados via POST e insere na tabela 'medicamentos'.
 */

require_once __DIR__ . '/../core.php';
require_once __DIR__ . '/../config/database.php';

// TODO: Adicionar verificação de permissão do usuário (ex: fn_usuario_tem_permissao($_SESSION['user_id'], 'medicamentos_criar'))

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

// Validação simples dos dados recebidos
if (
    empty($data->codigo) || empty($data->nome) || empty($data->classe_terapeutica_id) ||
    empty($data->tarja) || empty($data->forma_retirada) || empty($data->forma_fisica) ||
    empty($data->apresentacao) || empty($data->unidade_base) || !isset($data->dosagem_valor) ||
    empty($data->dosagem_unidade)
) {
    http_response_code(400);
    echo json_encode(["message" => "Dados incompletos. Verifique os campos obrigatórios."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query = "INSERT INTO medicamentos (codigo, nome, laboratorio_id, classe_terapeutica_id, tarja, forma_retirada, forma_fisica, apresentacao, unidade_base, dosagem_valor, dosagem_unidade, generico, limite_minimo, ativo) 
          VALUES (:codigo, :nome, :laboratorio_id, :classe_terapeutica_id, :tarja, :forma_retirada, :forma_fisica, :apresentacao, :unidade_base, :dosagem_valor, :dosagem_unidade, :generico, :limite_minimo, TRUE)";

$stmt = $db->prepare($query);

// Limpeza dos dados
$stmt->bindParam(':codigo', $data->codigo);
$stmt->bindParam(':nome', $data->nome);
$stmt->bindParam(':laboratorio_id', $data->laboratorio_id, PDO::PARAM_INT);
$stmt->bindParam(':classe_terapeutica_id', $data->classe_terapeutica_id, PDO::PARAM_INT);
$stmt->bindParam(':tarja', $data->tarja);
$stmt->bindParam(':forma_retirada', $data->forma_retirada);
$stmt->bindParam(':forma_fisica', $data->forma_fisica);
$stmt->bindParam(':apresentacao', $data->apresentacao);
$stmt->bindParam(':unidade_base', $data->unidade_base);
$stmt->bindParam(':dosagem_valor', $data->dosagem_valor);
$stmt->bindParam(':dosagem_unidade', $data->dosagem_unidade);
$stmt->bindParam(':generico', $data->generico, PDO::PARAM_BOOL);
$stmt->bindParam(':limite_minimo', $data->limite_minimo);

try {
    $stmt->execute();
    http_response_code(201); // Created
    echo json_encode(["message" => "Medicamento criado com sucesso."]);
} catch (PDOException $e) {
    if ($e->getCode() == '23505') { // UNIQUE violation
        http_response_code(409);
        echo json_encode(["message" => "O código do medicamento já existe."]);
    } else {
        http_response_code(503); // Service Unavailable
        echo json_encode(["message" => "Não foi possível criar o medicamento.", "error" => $e->getMessage()]);
    }
}
?>