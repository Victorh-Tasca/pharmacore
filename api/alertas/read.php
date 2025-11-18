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
    $query_validade = "SELECT v.medicamento, v.validade, v.status 
                       FROM vw_alerta_validade v
                       JOIN lotes l ON v.lote_id = l.id
                       WHERE v.quantidade_disponivel > 0 AND l.ativo = TRUE
                       ORDER BY v.dias_para_vencimento ASC";
    $stmt_validade = $db->prepare($query_validade);
    $stmt_validade->execute();
    $alertas_validade = $stmt_validade->fetchAll(PDO::FETCH_ASSOC);

    $query_estoque = "SELECT v.nome, v.quantidade_disponivel, v.limite_minimo 
                      FROM vw_alerta_estoque_baixo v
                      JOIN medicamentos m ON v.medicamento_id = m.id
                      WHERE m.ativo = TRUE
                      ORDER BY v.nome ASC";
    $stmt_estoque = $db->prepare($query_estoque);
    $stmt_estoque->execute();
    $alertas_estoque = $stmt_estoque->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        "validade" => $alertas_validade,
        "estoque_baixo" => $alertas_estoque
    ];

    http_response_code(200);
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao buscar dados: " . $e->getMessage()]);
}
?>