<?php
/**
 * V2 AuthResource — Token-based authentication endpoints.
 *
 * POST /api/v2/login   → login()
 * POST /api/v2/logout  → logout()
 * GET  /api/v2/me      → me()  (protected)
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/v2/ApiUser.php';
require_once __DIR__ . '/../../models/v2/ApiToken.php';
require_once __DIR__ . '/../../core/V2_AuthFilter.php';

class V2_AuthResource
{
    private $db;

    public function __construct()
    {
        $database  = new Database();
        $this->db  = $database->getConnection();
    }

    // POST /api/v2/login
    public function login()
    {
        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->username) || empty($data->password)) {
            http_response_code(400);
            echo json_encode([
                "error"   => "bad_request",
                "message" => "Se requieren username y password"
            ]);
            return;
        }

        $username = trim($data->username);
        $password = $data->password;

        $apiUser = new V2_ApiUser($this->db);
        $found   = $apiUser->findByUsername($username);

        // Constant-time comparison to prevent timing attacks
        $dummyHash  = '$2y$12$invalido.hash.que.nunca.coincide.con.nada.aqui';
        $hashToCheck = $found ? $apiUser->password_hash : $dummyHash;

        $passwordOk = password_verify($password, $hashToCheck);

        if (!$found || !$passwordOk || $apiUser->status !== 'ACTIVE') {
            http_response_code(401);
            echo json_encode([
                "error"   => "invalid_credentials",
                "message" => "Incorrect username or password"
            ]);
            return;
        }

        $apiToken = new V2_ApiToken($this->db);
        if (!$apiToken->create($apiUser->id)) {
            http_response_code(500);
            echo json_encode([
                "error"   => "server_error",
                "message" => "No se pudo generar el token de acceso"
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            "access_token" => $apiToken->token,
            "token_type"   => "Bearer",
            "expires_at"   => $apiToken->expires_at
        ]);
    }

    // POST /api/v2/logout
    public function logout()
    {
        header("Content-Type: application/json");

        $tokenString = $this->extractBearerToken();

        if (!$tokenString) {
            http_response_code(401);
            echo json_encode([
                "error"   => "unauthorized",
                "message" => "Invalid, expired, or missing token"
            ]);
            return;
        }

        $apiToken = new V2_ApiToken($this->db);

        if (!$apiToken->validate($tokenString)) {
            http_response_code(401);
            echo json_encode([
                "error"   => "unauthorized",
                "message" => "Invalid, expired, or missing token"
            ]);
            return;
        }

        if ($apiToken->revoke($tokenString)) {
            http_response_code(200);
            echo json_encode(["message" => "Sesión cerrada correctamente. Token revocado."]);
        } else {
            http_response_code(500);
            echo json_encode([
                "error"   => "server_error",
                "message" => "No se pudo revocar el token"
            ]);
        }
    }

    // GET /api/v2/me  (protected)
    public function me()
    {
        header("Content-Type: application/json");
        V2_AuthFilter::handle($this->db);
        http_response_code(200);
        echo json_encode(V2_AuthFilter::getUser());
    }

    private function extractBearerToken()
    {
        $authHeader = null;
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            foreach (apache_request_headers() as $key => $value) {
                if (strtolower($key) === 'authorization') {
                    $authHeader = $value;
                    break;
                }
            }
        }
        if ($authHeader && preg_match('/^Bearer\s+(\S+)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
?>
