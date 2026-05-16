<?php

/**
 * HELPERS GLOBALS — spa/app/helpers.php
 */

// ── Config ──────────────────────────────────────────────────────────────────
function config(string $key, mixed $default = null): mixed
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require SPA_PATH . '/config.php';
    }
    return $cfg[$key] ?? $default;
}

// ── View renderer ────────────────────────────────────────────────────────────
function view(string $name, array $data = []): void
{
    $path = SPA_PATH . '/resources/views/' . str_replace('.', '/', $name) . '.php';

    if (!file_exists($path)) {
        http_response_code(500);
        echo "<h1>View not found: {$name}</h1><p>{$path}</p>";
        exit;
    }

    extract($data, EXTR_SKIP);

    ob_start();
    require $path;
    $content = ob_get_clean();

    if (isset($layout)) {
        $layoutPath = SPA_PATH . '/resources/views/layouts/' . $layout . '.php';
        if (file_exists($layoutPath)) {
            require $layoutPath;
            return;
        }
    }

    echo $content;
}

// ── Redirect ─────────────────────────────────────────────────────────────────
function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

// ── URL helpers ───────────────────────────────────────────────────────────────
function url(string $path = ''): string
{
    return config('url') . '/' . ltrim($path, '/');
}

// ── Old input ───────────────────────────────────────────────────────────────
function old(string $key, string $default = ''): string
{
    return htmlspecialchars($_SESSION['_old_input'][$key] ?? $default, ENT_QUOTES);
}

// ── Escape ───────────────────────────────────────────────────────────────────
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// ── Flash messages ───────────────────────────────────────────────────────────
function flash(string $key, string $message): void
{
    session_start_if_not_started();
    $_SESSION['_flash'][$key] = $message;
}

function get_flash(string $key): ?string
{
    session_start_if_not_started();
    $msg = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $msg;
}

function session_start_if_not_started(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// ── API Base URL ─────────────────────────────────────────────────────────────
function api_url(string $path = ''): string
{
    return rtrim(config('api_url'), '/') . '/' . ltrim($path, '/');
}

// ── Active nav helper ────────────────────────────────────────────────────────
function active(string $path): string
{
    $uri = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
    if ($path === '/') {
        return ($uri === '/' || $uri === '/dashboard') ? 'active' : '';
    }
    return str_starts_with($uri, $path) ? 'active' : '';
}

// Start session
session_start_if_not_started();
