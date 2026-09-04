<?php
/**
 * V2 ApiToken model
 * Table: api_tokens
 */
class V2_ApiToken
{
    private $conn;
    private $table_name = "api_tokens";

    const TOKEN_EXPIRY_MINUTES = 60;

    public $id;
    public $user_id;
    public $token;
    public $expires_at;
    public $revoked;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    public function revokeAllForUser($user_id)
    {
        $query = "UPDATE " . $this->table_name . "
                  SET revoked = TRUE
                  WHERE user_id = :user_id AND revoked = FALSE";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
    }

    public function create($user_id)
    {
        $this->revokeAllForUser($user_id);

        $this->token      = $this->generateToken();
        $this->user_id    = $user_id;
        $this->expires_at = date('Y-m-d H:i:s', strtotime('+' . self::TOKEN_EXPIRY_MINUTES . ' minutes'));

        $query = "INSERT INTO " . $this->table_name . "
                  (user_id, token, expires_at)
                  VALUES (:user_id, :token, :expires_at)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id",    $this->user_id);
        $stmt->bindParam(":token",      $this->token);
        $stmt->bindParam(":expires_at", $this->expires_at);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function validate($token_string)
    {
        $query = "SELECT id, user_id, token, expires_at, revoked, created_at
                  FROM " . $this->table_name . "
                  WHERE token = :token
                    AND revoked = FALSE
                    AND expires_at > NOW()
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token_string);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id         = $row['id'];
            $this->user_id    = $row['user_id'];
            $this->token      = $row['token'];
            $this->expires_at = $row['expires_at'];
            $this->revoked    = $row['revoked'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    public function revoke($token_string)
    {
        $query = "UPDATE " . $this->table_name . "
                  SET revoked = TRUE
                  WHERE token = :token";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token_string);
        return $stmt->execute();
    }
}
?>
