<?php

/**
 * FRONT CONTROLLER UNIFICAT — index.php
 *
 * Un sol servidor, una sola comanda:
 *   php -S localhost:8000   (des de school-project/)
 *
 * - Peticions a /api/*  → delega a api.php (REST API, sense tocar res)
 * - Resta               → SPA Laravel-style (spa/)
 */

define('BASE_PATH', __DIR__);
define('SPA_PATH',  __DIR__ . '/spa');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ── API REST: /api/* ─────────────────────────────────────────────────────────
if (str_starts_with($uri, '/api')) {
    require __DIR__ . '/api.php';
    exit;
}

// ── SPA Laravel-style ────────────────────────────────────────────────────────
require SPA_PATH . '/bootstrap/app.php';
