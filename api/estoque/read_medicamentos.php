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
                v.medicamento_id,
                v.nome,
                v.codigo,
                v.unidade_base,
                v.quantidade_entrada,
                v.quantidade_saida,
                v.quantidade_disponivel,
                v.limite_minimo,
                v.alerta_minimo,
                v.alerta_menos_que_10_unidades,
                v.alerta_menos_que_20_porcento
            FROM 
                vw_estoque_por_medicamento v
            JOIN
                medicamentos m ON v.medicamento_id = m.id
            WHERE
                m.ativo = TRUE
            ORDER BY 
                v.nome";
    
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