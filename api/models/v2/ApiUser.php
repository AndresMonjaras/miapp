<?php
/**
 * V2 ApiUser model
 * Table: api_users (token-based auth users, separate from V1 users table)
 */
class V2_ApiUser
{
    private $conn;
    private $table_name = "api_users";

    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findByUsername($username)
    {
        $query = "SELECT id, username, email, password_hash, status, created_at, updated_at
                  FROM " . $this->table_name . "
                  WHERE username = :username
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id            = $row['id'];
            $this->username      = $row['username'];
            $this->email         = $row['email'];
            $this->password_hash = $row['password_hash'];
            $this->status        = $row['status'];
            $this->created_at    = $row['created_at'];
            $this->updated_at    = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function findById($id)
    {
        $query = "SELECT id, username, email, password_hash, status, created_at, updated_at
                  FROM " . $this->table_name . "
                  WHERE id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id            = $row['id'];
            $this->username      = $row['username'];
            $this->email         = $row['email'];
            $this->status        = $row['status'];
            $this->created_at    = $row['created_at'];
            $this->updated_at    = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function toSafeArray()
    {
        return [
            'id'         => $this->id,
            'username'   => $this->username,
            'email'      => $this->email,
            'status'     => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
?>
