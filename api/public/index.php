<?php
/**
 * API Entry Point
 *
 * Routes:
 *   /api/v1/*  →  V1 (original, no auth) — UserResource, ProductoResource
 *   /api/v2/*  →  V2 (new features, Bearer token auth) — AuthResource, UserResource, ProductoResource
 */

// ── CORS ────────────────────────────────────────────────────────────────────
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ── Core ────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/../core/Router.php';

// ── V1 Resources (no authentication) ────────────────────────────────────────
require_once __DIR__ . '/../resources/v1/UserResource.php';
require_once __DIR__ . '/../resources/v1/ProductoResource.php';

// ── V2 Resources (Bearer token authentication) ───────────────────────────────
require_once __DIR__ . '/../resources/v2/AuthResource.php';
require_once __DIR__ . '/../resources/v2/UserResource.php';
require_once __DIR__ . '/../resources/v2/ProductoResource.php';

// ── Tareas Resource (public) ────────────────────────────────────────────────
require_once __DIR__ . '/../resources/TareaResource.php';

// ── Base path (for sub-directory deployments) ────────────────────────────────
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

// ── Tareas Router ───────────────────────────────────────────────────────────
$routerTareas = new Router('tareas', $basePath);
$tareaResource = new TareaResource();

$routerTareas->addRoute('GET',    '',      [$tareaResource, 'list']);
$routerTareas->addRoute('GET',    '/{id}', [$tareaResource, 'show']);
$routerTareas->addRoute('POST',   '',      [$tareaResource, 'store']);
$routerTareas->addRoute('PUT',    '/{id}', [$tareaResource, 'update']);

// ── V1 Router ───────────────────────────────────────────────────────────────
$routerV1 = new Router('v1', $basePath);

$v1User     = new V1_UserResource();
$v1Producto = new V1_ProductoResource();

$routerV1->addRoute('GET',    '/users',          [$v1User, 'index']);
$routerV1->addRoute('GET',    '/users/{id}',      [$v1User, 'show']);
$routerV1->addRoute('POST',   '/users',          [$v1User, 'store']);
$routerV1->addRoute('PUT',    '/users/{id}',      [$v1User, 'update']);
$routerV1->addRoute('DELETE', '/users/{id}',      [$v1User, 'destroy']);

$routerV1->addRoute('GET',    '/productos',       [$v1Producto, 'index']);
$routerV1->addRoute('GET',    '/productos/{id}',  [$v1Producto, 'show']);
$routerV1->addRoute('POST',   '/productos',       [$v1Producto, 'store']);
$routerV1->addRoute('PUT',    '/productos/{id}',  [$v1Producto, 'update']);
$routerV1->addRoute('DELETE', '/productos/{id}',  [$v1Producto, 'destroy']);

// ── V2 Router ───────────────────────────────────────────────────────────────
$routerV2 = new Router('v2', $basePath);

$v2Auth     = new V2_AuthResource();
$v2User     = new V2_UserResource();
$v2Producto = new V2_ProductoResource();

// Auth routes (public — no token required)
$routerV2->addRoute('POST', '/login',  [$v2Auth, 'login']);
$routerV2->addRoute('POST', '/logout', [$v2Auth, 'logout']);
$routerV2->addRoute('GET',  '/me',     [$v2Auth, 'me']);

// User routes (protected)
$routerV2->addRoute('GET',    '/users',          [$v2User, 'index']);
$routerV2->addRoute('GET',    '/users/{id}',      [$v2User, 'show']);
$routerV2->addRoute('POST',   '/users',          [$v2User, 'store']);
$routerV2->addRoute('PUT',    '/users/{id}',      [$v2User, 'update']);
$routerV2->addRoute('DELETE', '/users/{id}',      [$v2User, 'destroy']);

// Producto routes (protected)
$routerV2->addRoute('GET',    '/productos',       [$v2Producto, 'index']);
$routerV2->addRoute('GET',    '/productos/{id}',  [$v2Producto, 'show']);
$routerV2->addRoute('POST',   '/productos',       [$v2Producto, 'store']);
$routerV2->addRoute('PUT',    '/productos/{id}',  [$v2Producto, 'update']);
$routerV2->addRoute('DELETE', '/productos/{id}',  [$v2Producto, 'destroy']);

// ── Dispatch ────────────────────────────────────────────────────────────────
if (!$routerTareas->dispatch() && !$routerV1->dispatch() && !$routerV2->dispatch()) {
    http_response_code(404);
    echo json_encode([
        "error"   => "not_found",
        "message" => "Ruta no encontrada",
        "uri"     => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
    ]);
}
?>
