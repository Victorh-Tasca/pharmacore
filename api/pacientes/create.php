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
    !isset($data->nome) || empty(trim($data->nome)) ||
    !isset($data->cpf) || empty(trim($data->cpf))
) {
    http_response_code(400);
    echo json_encode(["message" => "Nome e CPF são obrigatórios."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query = "INSERT INTO pacientes (nome, cpf, telefone, cidade) 
          VALUES (:nome, :cpf, :telefone, :cidade)";

try {
    $stmt = $db->prepare($query);

    $nome = htmlspecialchars(strip_tags($data->nome));
    $cpf = htmlspecialchars(strip_tags($data->cpf));
    $telefone = isset($data->telefone) ? htmlspecialchars(strip_tags($data->telefone)) : null;
    $cidade = isset($data->cidade) ? htmlspecialchars(strip_tags($data->cidade)) : null;

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':cidade', $cidade);

    $stmt->execute();
    
    http_response_code(201);
    echo json_encode(["message" => "Paciente criado com sucesso."]);

} catch (PDOException $e) {
    if ($e->getCode() == '23505') {
        http_response_code(409);
        echo json_encode(["message" => "Este CPF já está cadastrado."]);
    } else if ($e->getCode() == '23514') {
        http_response_code(400);
        echo json_encode(["message" => "O CPF informado é inválido."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Não foi possível criar o paciente."]);
    }
}
?>