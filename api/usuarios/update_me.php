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
$user_id = $_SESSION['user_id'];

if (!isset($data->nome) || empty(trim($data->nome))) {
    http_response_code(400);
    echo json_encode(["message" => "O nome é obrigatório."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query = "UPDATE usuarios SET nome = :nome, celular = :celular WHERE id = :id";

try {
    $stmt = $db->prepare($query);

    $nome = htmlspecialchars(strip_tags($data->nome));
    $celular = isset($data->celular) ? htmlspecialchars(strip_tags($data->celular)) : null;

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':celular', $celular);
    $stmt->bindParam(':id', $user_id);

    $stmt->execute();
    
    $_SESSION['user_name'] = $nome;

    http_response_code(200);
    echo json_encode(["message" => "Dados atualizados com sucesso.", "user_name" => $nome]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Não foi possível atualizar os dados."]);
}
?>