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

if (!isset($data->nome) || empty(trim($data->nome))) {
    http_response_code(400);
    echo json_encode(["message" => "O nome do papel é obrigatório."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query_papel = "INSERT INTO papeis (nome, descricao) VALUES (:nome, :descricao) RETURNING id";
$query_perm = "INSERT INTO papeis_permissoes (papel_id, permissao_id) VALUES (:papel_id, :permissao_id)";

try {
    $db->beginTransaction();

    $stmt_papel = $db->prepare($query_papel);
    $descricao = isset($data->descricao) ? htmlspecialchars(strip_tags($data->descricao)) : null;
    $stmt_papel->bindParam(':nome', $data->nome);
    $stmt_papel->bindParam(':descricao', $descricao);
    
    $stmt_papel->execute();
    $papel_id = $stmt_papel->fetchColumn();

    if (isset($data->permissoes) && is_array($data->permissoes)) {
        $stmt_perm = $db->prepare($query_perm);
        foreach ($data->permissoes as $permissao_id) {
            $stmt_perm->bindParam(':papel_id', $papel_id);
            $stmt_perm->bindParam(':permissao_id', $permissao_id);
            $stmt_perm->execute();
        }
    }

    $db->commit();
    http_response_code(201);
    echo json_encode(["message" => "Papel criado com sucesso."]);

} catch (PDOException $e) {
    $db->rollBack();
    if ($e->getCode() == '23505') {
        http_response_code(409);
        echo json_encode(["message" => "Este nome de papel já existe."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Não foi possível criar o papel."]);
    }
}
?>