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

if (!isset($data->id) || empty(trim($data->id))) {
    http_response_code(400);
    echo json_encode(["message" => "O ID é obrigatório."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query = "DELETE FROM usuarios WHERE id = :id";

try {
    $stmt = $db->prepare($query);
    $id = htmlspecialchars(strip_tags($data->id));
    $stmt->bindParam(':id', $id);

    $stmt->execute();
    
    http_response_code(200);
    echo json_encode(["message" => "Usuário excluído com sucesso."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Não foi possível excluir o usuário."]);
}
?>