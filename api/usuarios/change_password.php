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

if (
    !isset($data->senha_atual) || empty(trim($data->senha_atual)) ||
    !isset($data->nova_senha) || empty(trim($data->nova_senha))
) {
    http_response_code(400);
    echo json_encode(["message" => "Todos os campos são obrigatórios."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

try {
    $query_check = "SELECT senha_hash FROM usuarios WHERE id = :id AND senha_hash = crypt(:senha_atual, senha_hash)";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':id', $user_id);
    $stmt_check->bindParam(':senha_atual', $data->senha_atual);
    $stmt_check->execute();

    if ($stmt_check->rowCount() == 0) {
        http_response_code(401);
        echo json_encode(["message" => "A senha atual está incorreta."]);
        exit();
    }

    $query_update = "UPDATE usuarios SET senha_hash = :nova_senha WHERE id = :id";
    $stmt_update = $db->prepare($query_update);
    $stmt_update->bindParam(':nova_senha', $data->nova_senha);
    $stmt_update->bindParam(':id', $user_id);
    $stmt_update->execute();

    http_response_code(200);
    echo json_encode(["message" => "Senha alterada com sucesso."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Não foi possível alterar a senha."]);
}
?>