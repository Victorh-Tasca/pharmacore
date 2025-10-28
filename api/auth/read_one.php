<?php
/**
 * Endpoint para LER os dados de um único medicamento.
 * Recebe um 'id' via GET e retorna os dados completos para o formulário de edição.
 */

require_once __DIR__ . '/../core.php';
require_once __DIR__ . '/../config/database.php';

// TODO: Adicionar verificação de permissão do usuário

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : die();

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query = "SELECT * FROM medicamentos WHERE id = :id LIMIT 1";

$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$num = $stmt->rowCount();

if ($num > 0) {
    $medicamento = $stmt->fetch(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode($medicamento);
} else {
    http_response_code(404);
    echo json_encode(["message" => "Medicamento não encontrado."]);
}
?>