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

function asset_vite($entry)
{
    $isDev = false;
    $devServerUrl = 'http://localhost:5173';

    if (class_exists('Environment')) {
        $isDev = Environment::get('VITE_ENV') === 'development';
    }

    if ($isDev) {
        // Return Vite dev server tags
        // To make hot module reloading work, we also load Vite's client script
        $html = "<script type='module' src='{$devServerUrl}/@vite/client'></script>\n    ";
        if (str_ends_with($entry, '.scss') || str_ends_with($entry, '.css')) {
            $html .= "<link rel='stylesheet' href='{$devServerUrl}/{$entry}'>";
        } else {
            $html .= "<script type='module' src='{$devServerUrl}/{$entry}'></script>";
        }
        return $html;
    }

    // Production: read manifest
    $manifestPath = __DIR__ . '/../public/build/manifest.json';
    if (!file_exists($manifestPath)) {
        $manifestPath = __DIR__ . '/../public/build/.vite/manifest.json';
    }

    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        if (isset($manifest[$entry])) {
            $file = $manifest[$entry]['file'];
            $url = "/build/{$file}";
            
            if (str_ends_with($file, '.css')) {
                return "<link rel='stylesheet' href='{$url}'>";
            }
            
            $html = "<script type='module' src='{$url}'></script>";
            
            // If the script imports CSS, load that too
            if (isset($manifest[$entry]['css'])) {
                foreach ($manifest[$entry]['css'] as $cssFile) {
                    $html .= "\n    <link rel='stylesheet' href='/build/{$cssFile}'>";
                }
            }
            
            return $html;
        }
    }

    return "";
}
