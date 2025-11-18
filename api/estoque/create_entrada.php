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

if (
    !isset($data->medicamento_id) || empty($data->medicamento_id) ||
    !isset($data->validade) || empty($data->validade) ||
    !isset($data->data_fabricacao) || empty($data->data_fabricacao) ||
    !isset($data->fornecedor_id) || empty($data->fornecedor_id) ||
    !isset($data->numero_lote_fornecedor) || empty($data->numero_lote_fornecedor) ||
    !isset($data->quantidade_informada) || empty($data->quantidade_informada) ||
    !isset($data->unidade) || empty($data->unidade)
) {
    http_response_code(400);
    echo json_encode(["message" => "Todos os campos obrigatórios (*) devem ser preenchidos."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query_find_lote = "SELECT id FROM lotes WHERE medicamento_id = :medicamento_id AND validade_mes = date_trunc('month', :validade::date)";
$query_create_lote = "INSERT INTO lotes (medicamento_id, data_fabricacao, validade) 
                      VALUES (:medicamento_id, :data_fabricacao, :validade) RETURNING id";
$query_create_entrada = "INSERT INTO entradas (
                            fornecedor_id, lote_id, numero_lote_fornecedor, 
                            quantidade_informada, unidade, unidades_por_embalagem, 
                            estado, observacao
                         ) VALUES (
                            :fornecedor_id, :lote_id, :numero_lote_fornecedor,
                            :quantidade_informada, :unidade, :unidades_por_embalagem,
                            :estado, :observacao
                         )";

try {
    $db->beginTransaction();

    $stmt_find = $db->prepare($query_find_lote);
    $stmt_find->bindParam(':medicamento_id', $data->medicamento_id);
    $stmt_find->bindParam(':validade', $data->validade);
    $stmt_find->execute();

    $lote_id = $stmt_find->fetchColumn();

    if (!$lote_id) {
        $stmt_create_lote = $db->prepare($query_create_lote);
        $stmt_create_lote->bindParam(':medicamento_id', $data->medicamento_id);
        $stmt_create_lote->bindParam(':data_fabricacao', $data->data_fabricacao);
        $stmt_create_lote->bindParam(':validade', $data->validade);
        $stmt_create_lote->execute();
        $lote_id = $stmt_create_lote->fetchColumn();
    }

    $stmt_entrada = $db->prepare($query_create_entrada);
    
    $unidades_por_embalagem = isset($data->unidades_por_embalagem) && !empty($data->unidades_por_embalagem) ? $data->unidades_por_embalagem : null;
    $estado = isset($data->estado) && !empty($data->estado) ? $data->estado : 'novo';
    $observacao = isset($data->observacao) ? $data->observacao : null;

    $stmt_entrada->bindParam(':fornecedor_id', $data->fornecedor_id);
    $stmt_entrada->bindParam(':lote_id', $lote_id);
    $stmt_entrada->bindParam(':numero_lote_fornecedor', $data->numero_lote_fornecedor);
    $stmt_entrada->bindParam(':quantidade_informada', $data->quantidade_informada);
    $stmt_entrada->bindParam(':unidade', $data->unidade);
    $stmt_entrada->bindParam(':unidades_por_embalagem', $unidades_por_embalagem);
    $stmt_entrada->bindParam(':estado', $estado);
    $stmt_entrada->bindParam(':observacao', $observacao);

    $stmt_entrada->execute();

    $db->commit();
    http_response_code(201);
    echo json_encode(["message" => "Entrada registrada com sucesso."]);

} catch (PDOException $e) {
    $db->rollBack();
    if ($e->getCode() == '23505') {
        http_response_code(409);
        echo json_encode(["message" => "Erro de Duplicidade: O Lote do Fornecedor para este Lote/Validade já foi inserido."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Não foi possível registrar a entrada: " . $e->getMessage()]);
    }
}
?>