<?php

function sanitiza($html)
{
    return htmlspecialchars($html);
}
function redireccionar($url)
{
    header("Location: $url");
    exit;
}

function Authphp()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION["login"]) || $_SESSION["login"] !== true) {
        redireccionar('/login');
    }
}
