<?php
require_once __DIR__ . '/../core.php';


if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    http_response_code(200);
    echo json_encode([
        "logged_in" => true,
        "user_id" => $_SESSION['user_id'],
        "user_name" => $_SESSION['user_name']
    ]);
} else {
    http_response_code(401); 
    echo json_encode([
        "logged_in" => false,
        "message" => "Usuário não autenticado."
    ]);
}
?>