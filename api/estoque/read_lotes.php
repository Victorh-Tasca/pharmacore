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
    $query = "SELECT 
                v.lote_id,
                v.medicamento_id,
                v.medicamento,
                v.codigo,
                v.tarja,
                v.quantidade_disponivel,
                v.validade,
                v.dias_para_vencimento,
                v.status
            FROM 
                vw_estoque_por_lote v
            JOIN
                lotes l ON v.lote_id = l.id
            WHERE
                v.quantidade_disponivel > 0 AND l.ativo = TRUE
            ORDER BY 
                v.dias_para_vencimento ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($items);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao buscar dados: " . $e->getMessage()]);
}
?>