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
    !isset($data->codigo) || empty(trim($data->codigo)) ||
    !isset($data->nome) || empty(trim($data->nome)) ||
    !isset($data->classe_terapeutica_id) || empty(trim($data->classe_terapeutica_id)) ||
    !isset($data->laboratorio_id) || empty(trim($data->laboratorio_id))
) {
    http_response_code(400);
    echo json_encode(["message" => "Campos obrigatórios (Código, Nome, Classe, Laboratório) não preenchidos."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Erro ao conectar ao banco de dados."]);
    exit();
}

$query = "INSERT INTO medicamentos (
            codigo, nome, laboratorio_id, classe_terapeutica_id, tarja, 
            forma_retirada, forma_fisica, apresentacao, unidade_base, 
            dosagem_valor, dosagem_unidade, generico, limite_minimo, serial_por_classe
          ) VALUES (
            :codigo, :nome, :laboratorio_id, :classe_terapeutica_id, :tarja, 
            :forma_retirada, :forma_fisica, :apresentacao, :unidade_base, 
            :dosagem_valor, :dosagem_unidade, :generico, :limite_minimo, 0
          )";

try {
    $stmt = $db->prepare($query);

    $stmt->bindParam(':codigo', $data->codigo);
    $stmt->bindParam(':nome', $data->nome);
    $stmt->bindParam(':laboratorio_id', $data->laboratorio_id);
    $stmt->bindParam(':classe_terapeutica_id', $data->classe_terapeutica_id);
    $stmt->bindParam(':tarja', $data->tarja);
    $stmt->bindParam(':forma_retirada', $data->forma_retirada);
    $stmt->bindParam(':forma_fisica', $data->forma_fisica);
    $stmt->bindParam(':apresentacao', $data->apresentacao);
    $stmt->bindParam(':unidade_base', $data->unidade_base);
    $stmt->bindParam(':dosagem_valor', $data->dosagem_valor);
    $stmt->bindParam(':dosagem_unidade', $data->dosagem_unidade);
    $stmt->bindParam(':generico', $data->generico, PDO::PARAM_BOOL);
    $stmt->bindParam(':limite_minimo', $data->limite_minimo);

    $stmt->execute();
    
    http_response_code(201);
    echo json_encode(["message" => "Medicamento criado com sucesso."]);

} catch (PDOException $e) {
    if ($e->getCode() == '23505') {
        http_response_code(409);
        echo json_encode(["message" => "Este Código de medicamento já existe."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Não foi possível criar o medicamento."]);
    }
}
?>