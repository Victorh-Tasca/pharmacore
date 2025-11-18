<?php
class Database {

    private $database_url;
    public $conn;

    public function __construct() {

        $this->database_url = getenv('DATABASE_URL');
    }
    
public function getConnection() {

    $this->conn = null;

    if ($this->database_url === false || $this->database_url === null) {
        // MUDANÇA: Vamos "lançar" uma exceção que o register.php pode pegar
        throw new Exception("Erro Crítico: DATABASE_URL não definida ou não carregada.");
    }

    $db_parts = parse_url($this->database_url);

    if ($db_parts === false) {
        throw new Exception("Erro: A DATABASE_URL está mal formatada.");
    }

    $host     = $db_parts['host'] ?? null;
    $port     = $db_parts['port'] ?? 5432; 
    $username = $db_parts['user'] ?? null;
    $password = $db_parts['pass'] ?? null; 
    $db_name  = ltrim($db_parts['path'], '/'); 

    try {
        $dsn = "pgsql:host=" . $host . ";port=" . $port . ";dbname=" . $db_name;
        $this->conn = new PDO($dsn, $username, $password); 
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $exception) {
        // MUDANÇA: Lança a exceção de PDO
        throw new Exception("Erro de Conexão PDO: " . $exception->getMessage());
    }

    return $this->conn;
}
}
