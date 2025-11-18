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
    !isset($data->email) || empty(trim($data->email)) ||
    !isset($data->login) || empty(trim($data->login))
) {
    http_response_code(400);
    echo json_encode(["message" => "ID, Nome, Email e Login são obrigatórios."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}


$query_user = "UPDATE usuarios SET 
                    nome = :nome, 
                    email = :email, 
                    login = :login, 
                    celular = :celular, 
                    ativo = :ativo";

if (isset($data->senha) && !empty($data->senha)) {
    $query_user .= ", senha_hash = :senha";
}
$query_user .= " WHERE id = :id";

$query_delete_papeis = "DELETE FROM usuarios_papeis WHERE usuario_id = :usuario_id";
$query_insert_papeis = "INSERT INTO usuarios_papeis (usuario_id, papel_id) VALUES (:usuario_id, :papel_id)";

try {
    $db->beginTransaction();
    
    $stmt_user = $db->prepare($query_user);

    $ativo = isset($data->ativo) ? (bool)$data->ativo : true;
    $celular = isset($data->celular) ? htmlspecialchars(strip_tags($data->celular)) : null;

    $stmt_user->bindParam(':id', $data->id);
    $stmt_user->bindParam(':nome', $data->nome);
    $stmt_user->bindParam(':email', $data->email);
    $stmt_user->bindParam(':login', $data->login);
    $stmt_user->bindParam(':celular', $celular);
    $stmt_user->bindParam(':ativo', $ativo, PDO::PARAM_BOOL);

    if (isset($data->senha) && !empty($data->senha)) {
        $stmt_user->bindParam(':senha', $data->senha);
    }

    $stmt_user->execute();

    $stmt_delete = $db->prepare($query_delete_papeis);
    $stmt_delete->bindParam(':usuario_id', $data->id);
    $stmt_delete->execute();

    if (isset($data->papeis) && is_array($data->papeis)) {
        $stmt_insert = $db->prepare($query_insert_papeis);
        foreach ($data->papeis as $papel_id) {
            $stmt_insert->bindParam(':usuario_id', $data->id);
            $stmt_insert->bindParam(':papel_id', $papel_id);
            $stmt_insert->execute();
        }
    }
    
    $db->commit();
    http_response_code(200);
    echo json_encode(["message" => "Usuário atualizado com sucesso."]);

} catch (PDOException $e) {
    $db->rollBack();
    if ($e->getCode() == '23505') {
        http_response_code(409);
        echo json_encode(["message" => "Este Email ou Login já está em uso."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Não foi possível atualizar o usuário."]);
    }
}
?>