<?php

/**
 * API FRONT CONTROLLER: api.php
 *
 * Punt d'entrada per a totes les peticions REST.
 * Accedeix via: http://localhost:8000/api.php
 *
 * Exemples:
 *   GET    /api.php/api/students
 *   POST   /api.php/api/students       (body JSON)
 *   GET    /api.php/api/students/{id}
 *   DELETE /api.php/api/students/{id}
 *   GET    /api.php/api/teachers
 *   POST   /api.php/api/teachers       (body JSON)
 *   GET    /api.php/api/subjects
 *   POST   /api.php/api/subjects       (body JSON)
 *   POST   /api.php/api/subjects/{id}/assign-teacher
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

use App\Http\Request;
use App\Http\Routing\RouteCollection;
use App\Http\Routing\Router;
use App\Http\Middleware\ApiAuthMiddleware;

// ── 0. Carregar .env ──────────────────────────────────────────────────────
if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/.env')) {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

// ── 1. Inicialitzar Doctrine ──────────────────────────────────────────────
try {
    $em = require __DIR__ . '/config/doctrine.php';
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// ── 2. Crear les taules automàticament si no existeixen ──────────────────
try {
    $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
    $allClasses = [
        $em->getClassMetadata(\App\Domain\Student\Student::class),
        $em->getClassMetadata(\App\Domain\Course\Course::class),
        $em->getClassMetadata(\App\Domain\Subject\Subject::class),
        $em->getClassMetadata(\App\Domain\Teacher\Teacher::class),
        $em->getClassMetadata(\App\Domain\Enrollment\Enrollment::class),
        $em->getClassMetadata(\App\Domain\Assignment\Assignment::class),
    ];
    $schemaTool->updateSchema($allClasses, true);
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Schema update failed: ' . $e->getMessage()]);
    exit;
}

// ── 3. Autenticació API ──────────────────────────────────────────────────
// Envia CORS headers primer (preflight ja gestionat a l'OPTIONS)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');
ApiAuthMiddleware::handle();

// ── 4. Carregar rutes i fer dispatch ─────────────────────────────────────
$routes = new RouteCollection(__DIR__ . '/config/api_routes.php');
$router = new Router($routes);
$req    = new Request();

$router->dispatch($req, $em);
