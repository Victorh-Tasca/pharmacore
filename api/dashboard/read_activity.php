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
    $query = "
    (
        SELECT 
            'Entrada' AS tipo,
            m.nome AS produto,
            e.data_entrada AS data,
            f.nome AS responsavel
        FROM entradas e
        JOIN lotes l ON e.lote_id = l.id
        JOIN medicamentos m ON l.medicamento_id = m.id
        JOIN fornecedores f ON e.fornecedor_id = f.id
        ORDER BY data DESC
        LIMIT 5
    )
    UNION ALL
    (
        SELECT 
            'Saída' AS tipo,
            m.nome AS produto,
            d.data_dispensa AS data,
            COALESCE(d.responsavel, p.nome) AS responsavel
        FROM dispensacoes d
        JOIN pacientes p ON d.paciente_id = p.id
        JOIN lotes l ON d.lote_id = l.id
        JOIN medicamentos m ON l.medicamento_id = m.id
        ORDER BY data DESC
        LIMIT 5
    )
    ORDER BY data DESC
    LIMIT 10
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute();

    $activity = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($activity);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao buscar atividades: " . $e->getMessage()]);
}
?>