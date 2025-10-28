<?php
/**
 * Endpoint para ATUALIZAR um medicamento existente.
 * Recebe os dados via POST, incluindo o ID do item a ser atualizado.
 */

require_once __DIR__ . '/../core.php';
require_once __DIR__ . '/../config/database.php';

// TODO: Adicionar verificação de permissão do usuário

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->id)) {
    http_response_code(400);
    echo json_encode(["message" => "ID do medicamento não fornecido."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query = "UPDATE medicamentos SET 
            codigo = :codigo, nome = :nome, laboratorio_id = :laboratorio_id, 
            classe_terapeutica_id = :classe_terapeutica_id, tarja = :tarja, forma_retirada = :forma_retirada, 
            forma_fisica = :forma_fisica, apresentacao = :apresentacao, unidade_base = :unidade_base, 
            dosagem_valor = :dosagem_valor, dosagem_unidade = :dosagem_unidade, generico = :generico, 
            limite_minimo = :limite_minimo, ativo = :ativo
          WHERE id = :id";

$stmt = $db->prepare($query);

$stmt->bindParam(':id', $data->id, PDO::PARAM_INT);
$stmt->bindParam(':codigo', $data->codigo);
$stmt->bindParam(':nome', $data->nome);
$stmt->bindParam(':laboratorio_id', $data->laboratorio_id, PDO::PARAM_INT);
$stmt->bindParam(':classe_terapeutica_id', $data->classe_terapeutica_id, PDO::PARAM_INT);
$stmt->bindParam(':tarja', $data->tarja);
$stmt->bindParam(':forma_retirada', $data->forma_retirada);
$stmt->bindParam(':forma_fisica', $data->forma_fisica);
$stmt->bindParam(':apresentacao', $data->apresentacao);
$stmt->bindParam(':unidade_base', $data->unidade_base);
$stmt->bindParam(':dosagem_valor', $data->dosagem_valor);
$stmt->bindParam(':dosagem_unidade', $data->dosagem_unidade);
$stmt->bindParam(':generico', $data->generico, PDO::PARAM_BOOL);
$stmt->bindParam(':limite_minimo', $data->limite_minimo);
$stmt->bindParam(':ativo', $data->ativo, PDO::PARAM_BOOL);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "Medicamento atualizado com sucesso."]);
} else {
    http_response_code(503);
    echo json_encode(["message" => "Não foi possível atualizar o medicamento."]);
}
?>