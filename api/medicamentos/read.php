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
                m.id, 
                m.codigo, 
                m.nome, 
                c.nome AS classe_terapeutica, 
                l.nome AS laboratorio, 
                m.tarja, 
                m.ativo,
                m.laboratorio_id,
                m.classe_terapeutica_id,
                m.forma_retirada,
                m.forma_fisica,
                m.apresentacao,
                m.unidade_base,
                m.dosagem_valor,
                m.dosagem_unidade,
                m.generico,
                m.limite_minimo
            FROM 
                medicamentos m
            LEFT JOIN 
                classes_terapeuticas c ON m.classe_terapeutica_id = c.id
            LEFT JOIN 
                laboratorios l ON m.laboratorio_id = l.id
            ORDER BY 
                m.nome";
    
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