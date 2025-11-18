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
    !isset($data->nome) || empty(trim($data->nome))
) {
    http_response_code(400);
    echo json_encode(["message" => "ID e Nome são obrigatórios."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query_papel = "UPDATE papeis SET nome = :nome, descricao = :descricao WHERE id = :id";
$query_delete_perm = "DELETE FROM papeis_permissoes WHERE papel_id = :papel_id";
$query_insert_perm = "INSERT INTO papeis_permissoes (papel_id, permissao_id) VALUES (:papel_id, :permissao_id)";

try {
    $db->beginTransaction();

    $stmt_papel = $db->prepare($query_papel);
    $descricao = isset($data->descricao) ? htmlspecialchars(strip_tags($data->descricao)) : null;
    $stmt_papel->bindParam(':id', $data->id);
    $stmt_papel->bindParam(':nome', $data->nome);
    $stmt_papel->bindParam(':descricao', $descricao);
    $stmt_papel->execute();

    $stmt_delete = $db->prepare($query_delete_perm);
    $stmt_delete->bindParam(':papel_id', $data->id);
    $stmt_delete->execute();

    if (isset($data->permissoes) && is_array($data->permissoes)) {
        $stmt_insert = $db->prepare($query_insert_perm);
        foreach ($data->permissoes as $permissao_id) {
            $stmt_insert->bindParam(':papel_id', $data->id);
            $stmt_insert->bindParam(':permissao_id', $permissao_id);
            $stmt_insert->execute();
        }
    }

    $db->commit();
    http_response_code(200);
    echo json_encode(["message" => "Papel atualizado com sucesso."]);

} catch (PDOException $e) {
    $db->rollBack();
    if ($e->getCode() == '23505') {
        http_response_code(409);
        echo json_encode(["message" => "Este nome de papel já existe."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Não foi possível atualizar o papel."]);
    }
}
?>