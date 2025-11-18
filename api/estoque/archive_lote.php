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

if (!isset($data->lote_id) || empty(trim($data->lote_id))) {
    http_response_code(400);
    echo json_encode(["message" => "O ID do lote é obrigatório."]);
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
    $query = "SELECT fn_arquivar_lote_se_sem_saldo_ou_vencido(:lote_id)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':lote_id', $data->lote_id);
    $stmt->execute();
    
    $result = $stmt->fetchColumn();

    if ($result === true) {
        http_response_code(200);
        echo json_encode(["message" => "Lote arquivado com sucesso."]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "O lote não pode ser arquivado (ainda possui saldo e está dentro da validade)."]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao arquivar o lote: " . $e->getMessage()]);
}
?>