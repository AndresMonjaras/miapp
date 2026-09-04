<?php
session_start();

// ── Revoke V2 Bearer token before destroying session ─────────────────────
if (!empty($_SESSION['api_token'])) {
    $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host    = $_SERVER['HTTP_HOST'];
    $script  = $_SERVER['SCRIPT_NAME'];
    $root    = dirname(dirname(dirname($script)));
    $apiBase = $scheme . '://' . $host . $root . '/api/public';

    $ctx = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => "Authorization: Bearer " . $_SESSION['api_token'] . "\r\nContent-Length: 0",
            'content' => '',
            'timeout' => 5,
            'ignore_errors' => true,
        ]
    ]);
    @file_get_contents($apiBase . '/api/v2/logout', false, $ctx);
}

session_destroy();
header("Location: login.php");
exit;
?>
