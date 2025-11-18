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
    !isset($data->nome) || empty(trim($data->nome)) ||
    !isset($data->codigo_classe) || empty(trim($data->codigo_classe))
) {
    http_response_code(400);
    echo json_encode(["message" => "ID, Nome e Código da Classe são obrigatórios."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query = "UPDATE classes_terapeuticas SET nome = :nome, codigo_classe = :codigo_classe 
          WHERE id = :id";

try {
    $stmt = $db->prepare($query);

    $nome = htmlspecialchars(strip_tags($data->nome));
    $codigo_classe = htmlspecialchars(strip_tags($data->codigo_classe));
    $id = htmlspecialchars(strip_tags($data->id));

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':codigo_classe', $codigo_classe);
    $stmt->bindParam(':id', $id);

    $stmt->execute();
    
    http_response_code(200);
    echo json_encode(["message" => "Classe atualizada com sucesso."]);

} catch (PDOException $e) {
    if ($e->getCode() == '23505') {
        http_response_code(409);
        echo json_encode(["message" => "Este Nome ou Código de Classe já existe."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Não foi possível atualizar a classe."]);
    }
}
?>