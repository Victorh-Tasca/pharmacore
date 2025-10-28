<?php
require_once __DIR__ . '/../core.php';
require_once __DIR__ . '/../config/database.php';

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

if (!isset($data->email) || !isset($data->senha)) {
    http_response_code(400);
    echo json_encode(["message" => "E-mail e senha são obrigatórios."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

// O campo de login pode ser o e-mail ou o login do usuário
$query = "SELECT id, nome, email, senha_hash, ativo FROM usuarios WHERE email = :login OR login = :login";

$stmt = $db->prepare($query);

$login_input = htmlspecialchars(strip_tags($data->email));
$stmt->bindParam(':login', $login_input);

$stmt->execute();
$num = $stmt->rowCount();

if ($num > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    extract($row); // extrai $id, $nome, $email, $senha_hash, $ativo

    // O trigger no banco usa crypt(), então verificamos com a mesma função.
    // password_verify() é mais moderno, mas para compatibilidade com o trigger, usamos crypt().
    if ($ativo && hash_equals($senha_hash, crypt($data->senha, $senha_hash))) {
        
        // Regenera o ID da sessão para segurança
        session_regenerate_id(true);

        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $nome;
        $_SESSION['logged_in'] = true;

        http_response_code(200);
        echo json_encode(["message" => "Login bem-sucedido.", "redirect" => "../pharma-dashboard/index.html"]);
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(["message" => "Usuário ou senha inválidos."]);
    }
} else {
    http_response_code(401); // Unauthorized
    echo json_encode(["message" => "Usuário ou senha inválidos."]);
}