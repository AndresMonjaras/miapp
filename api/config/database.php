<?php
/**
 * Database connection configuration.
 *
 * Credentials match the production server exactly —
 * copy this file as-is when migrating local → server.
 */
class Database
{
    private $host     = "dbmy";
    private $db_name  = "tap";
    private $username = "root";
    private $password = "password";

    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8mb4");
        } catch (PDOException $e) {
            echo json_encode([
                "error"   => "db_connection_error",
                "message" => $e->getMessage()
            ]);
            exit();
        }
        return $this->conn;
    }
}
?>
