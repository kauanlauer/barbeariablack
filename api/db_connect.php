<?php
// =========================================================================
// ARQUIVO 2: /theblackbeard/api/db_connect.php
// Faz a conexão com o banco de dados usando PDO.
// =========================================================================

class Database {
    private $host = "localhost";
    private $db_name = "barbearia_db";
    private $username = "root";
    private $password = "";
    public $conn;

    // Método para obter a conexão com o banco de dados
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
