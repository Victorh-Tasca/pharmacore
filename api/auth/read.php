<?php
/**
 * Endpoint para LER a lista de medicamentos.
 * Retorna um JSON com todos os medicamentos (ou filtrados).
 */

require_once __DIR__ . '/../core.php';
require_once __DIR__ . '/../config/database.php';

// TODO: Adicionar verificação de permissão do usuário

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

// A query junta com laboratorios e classes_terapeuticas para obter os nomes
$query = "SELECT 
            m.id, m.codigo, m.nome, m.tarja, m.generico, m.ativo,
            l.nome as laboratorio_nome, 
            ct.nome as classe_terapeutica_nome
          FROM 
            medicamentos m
          LEFT JOIN 
            laboratorios l ON m.laboratorio_id = l.id
          LEFT JOIN 
            classes_terapeuticas ct ON m.classe_terapeutica_id = ct.id
          ORDER BY 
            m.nome ASC";

$stmt = $db->prepare($query);
$stmt->execute();

$num = $stmt->rowCount();

if ($num > 0) {
    $medicamentos_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode($medicamentos_arr);
} else {
    http_response_code(404);
    echo json_encode(["message" => "Nenhum medicamento encontrado."]);
}
?>