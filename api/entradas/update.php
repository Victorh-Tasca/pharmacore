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
    !isset($data->id) || empty(trim($data->id)) ||
    !isset($data->quantidade_informada) || empty(trim($data->quantidade_informada))
) {
    http_response_code(400);
    echo json_encode(["message" => "ID da Entrada e Quantidade são obrigatórios."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query = "UPDATE entradas SET 
            quantidade_informada = :quantidade_informada, 
            unidades_por_embalagem = :unidades_por_embalagem
          WHERE id = :id";

try {
    $stmt = $db->prepare($query);

    $unidades_por_embalagem = isset($data->unidades_por_embalagem) && !empty($data->unidades_por_embalagem) ? $data->unidades_por_embalagem : null;

    $stmt->bindParam(':id', $data->id);
    $stmt->bindParam(':quantidade_informada', $data->quantidade_informada);
    $stmt->bindParam(':unidades_por_embalagem', $unidades_por_embalagem);

    $stmt->execute();
    
    http_response_code(200);
    echo json_encode(["message" => "Entrada atualizada com sucesso."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Não foi possível atualizar a entrada."]);
}
?>