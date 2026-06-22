<?php

function sanitize($html)
{
    return htmlspecialchars($html);
}
function redirect($url)
{
    header("Location: $url");
    exit;
}

function auth($rol = null)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION["login"]) || $_SESSION["login"] !== true) {
        redirect('/login');
    }
    if ($rol !== null && $_SESSION["rol"] !== $rol) {
        redirect('/login');
    }
}
