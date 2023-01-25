<?php
class DB
{
    private $host = "localhost";
    private $db = "apirest";
    private $username = "root";
    private $password = "";

    public $conn;

    public function connect()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "No se pudo conectar la base de datos " . $exception->getMessage();
        }
        return $this->conn;
    }
}
