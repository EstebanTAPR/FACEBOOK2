<?php


class Database {
    private $host = "127.0.0.1";       
    private $db_name = "red_social";      
    private $username = "root";    
    private $password = "";  
    public $conn;

   
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Error en la conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
