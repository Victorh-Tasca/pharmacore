<?php
class Database {
    // --- PREENCHER COM AS CREDENCIAIS DO BANCO DE DADOS ---
    private $host = "localhost"; // ou o IP do servidor de banco de dados
    private $db_name = "farmacia"; // nome do banco de dados, conforme schema
    private $username = "postgres"; // usuário do banco de dados
    private $password = "sua_senha_aqui"; // senha do usuário
    private $port = "5432"; // porta padrão do PostgreSQL
    // ----------------------------------------------------

    public $conn;

    // obtém a conexão com o banco de dados
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Em produção, é melhor logar o erro do que exibi-lo
            // echo "Connection error: " . $exception->getMessage();
            return null;
        }

        return $this->conn;
    }
}
?>