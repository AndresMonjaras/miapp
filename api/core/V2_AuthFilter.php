<?php
/**
 * V2_AuthFilter — Bearer token authentication middleware for V2 routes.
 * Isolated from V1 (which has no auth).
 *
 * Usage:
 *   V2_AuthFilter::handle($db);        // aborts with 401 if token invalid
 *   $user = V2_AuthFilter::getUser();  // returns authenticated user array
 */

require_once __DIR__ . '/../models/v2/ApiToken.php';
require_once __DIR__ . '/../models/v2/ApiUser.php';

class V2_AuthFilter
{
    private static $authenticatedUserId = null;
    private static $authenticatedUser   = null;

    public static function handle($db)
    {
        header("Content-Type: application/json");

        $authHeader = self::getAuthorizationHeader();

        if (empty($authHeader)) {
            self::unauthorized();
        }

        if (!preg_match('/^Bearer\s+(\S+)$/i', $authHeader, $matches)) {
            self::unauthorized();
        }

        $tokenString = $matches[1];

        $apiToken = new V2_ApiToken($db);
        if (!$apiToken->validate($tokenString)) {
            self::unauthorized();
        }

        $apiUser = new V2_ApiUser($db);
        if (!$apiUser->findById($apiToken->user_id)) {
            self::unauthorized();
        }

        if ($apiUser->status !== 'ACTIVE') {
            self::unauthorized();
        }

        self::$authenticatedUserId = $apiUser->id;
        self::$authenticatedUser   = $apiUser->toSafeArray();
    }

    public static function getUserId()
    {
        return self::$authenticatedUserId;
    }

    public static function getUser()
    {
        return self::$authenticatedUser;
    }

    private static function getAuthorizationHeader()
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (function_exists('apache_request_headers')) {
            foreach (apache_request_headers() as $key => $value) {
                if (strtolower($key) === 'authorization') {
                    return $value;
                }
            }
        }
        return null;
    }

    private static function unauthorized($message = "Invalid, expired, or missing token")
    {
        http_response_code(401);
        echo json_encode([
            "error"   => "unauthorized",
            "message" => $message
        ]);
        exit();
    }
}
?>
