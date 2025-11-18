<?php
require_once __DIR__ . '/../core.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["message" => "Usuário não autenticado."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

try {
    $query = "SELECT 
                u.id, u.nome, u.login, u.email, u.celular, u.ativo,
                COALESCE(JSON_AGG(up.papel_id) FILTER (WHERE up.papel_id IS NOT NULL), '[]') AS papeis
            FROM 
                usuarios u
            LEFT JOIN 
                usuarios_papeis up ON u.id = up.usuario_id
            GROUP BY
                u.id
            ORDER BY 
                u.nome";
    
    $stmt = $db->prepare($query);
    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as &$item) {
        $item['papeis'] = json_decode($item['papeis']);
        $item['ativo'] = (bool)$item['ativo'];
    }

    http_response_code(200);
    echo json_encode($items);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao buscar dados: " . $e->getMessage()]);
}
?>