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

if (!isset($_GET['lote_id']) || empty($_GET['lote_id'])) {
     http_response_code(400);
    echo json_encode(["message" => "O ID do lote é obrigatório."]);
    exit();
}

$lote_id = $_GET['lote_id'];
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

try {
    $query = "SELECT 
                e.id, 
                e.data_entrada, 
                f.nome AS fornecedor, 
                e.numero_lote_fornecedor,
                e.quantidade_informada,
                e.unidade,
                e.unidades_por_embalagem,
                m.unidade_base
            FROM 
                entradas e
            JOIN 
                fornecedores f ON e.fornecedor_id = f.id
            JOIN
                lotes l ON e.lote_id = l.id
            JOIN
                medicamentos m ON l.medicamento_id = m.id
            WHERE 
                e.lote_id = :lote_id
            ORDER BY 
                e.data_entrada DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':lote_id', $lote_id);
    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($items);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao buscar dados: " . $e->getMessage()]);
}
?>