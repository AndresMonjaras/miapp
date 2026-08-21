<?php
function estaAutenticado() {
    return isset($_SESSION['usuario_id']);
}

function redirigir($url) {
    header("Location: $url");
    exit;
}

function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function sanitizar($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
?>
