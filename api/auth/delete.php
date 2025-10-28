<?php
/**
 * Endpoint para DELETAR (desativar) um medicamento.
 * Recebe um 'id' via POST e define o campo 'ativo' como false.
 */

require_once __DIR__ . '/../core.php';
require_once __DIR__ . '/../config/database.php';

// TODO: Adicionar verificação de permissão do usuário

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

if (empty($data->id)) {
    http_response_code(400);
    echo json_encode(["message" => "ID do medicamento não fornecido."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$query = "UPDATE medicamentos SET ativo = FALSE WHERE id = :id";

$stmt = $db->prepare($query);
$stmt->bindParam(':id', $data->id, PDO::PARAM_INT);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "Medicamento desativado com sucesso."]);
} else {
    http_response_code(503);
    echo json_encode(["message" => "Não foi possível desativar o medicamento."]);
}
?>