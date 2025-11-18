<?php
require_once __DIR__ . '/../core.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["message" => "Usuário não autenticado."]);
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
    $queries = [
        'total_produtos' => "SELECT COUNT(id) FROM medicamentos WHERE ativo = TRUE",
        'vencidos' => "SELECT COUNT(lote_id) FROM vw_alerta_validade WHERE status = 'Bloquear dispensação'",
        'prox_vencimento' => "SELECT COUNT(lote_id) FROM vw_alerta_validade WHERE dias_para_vencimento > 0 AND dias_para_vencimento <= 30",
        'estoque_baixo' => "SELECT COUNT(medicamento_id) FROM vw_alerta_estoque_baixo"
    ];

    $stats = [];
    foreach ($queries as $key => $query) {
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats[$key] = (int)$stmt->fetchColumn();
    }

    http_response_code(200);
    echo json_encode($stats);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao buscar estatísticas: " . $e->getMessage()]);
}
?>