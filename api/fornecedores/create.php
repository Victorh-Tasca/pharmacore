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
    !isset($data->tipo) || empty(trim($data->tipo))
) {
    http_response_code(400);
    echo json_encode(["message" => "Nome e Tipo são obrigatórios."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query = "INSERT INTO fornecedores (nome, tipo, contato) 
          VALUES (:nome, :tipo, :contato)";

try {
    $stmt = $db->prepare($query);

    $nome = htmlspecialchars(strip_tags($data->nome));
    $tipo = htmlspecialchars(strip_tags($data->tipo));
    $contato = isset($data->contato) ? htmlspecialchars(strip_tags($data->contato)) : null;

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':contato', $contato);

    $stmt->execute();
    
    http_response_code(201);
    echo json_encode(["message" => "Fornecedor criado com sucesso."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Não foi possível criar o fornecedor."]);
}
?>