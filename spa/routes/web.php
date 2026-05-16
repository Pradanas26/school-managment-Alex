<?php

/**
 * ROUTES — routes/web.php
 *
 * Equivalent al routes/web.php de Laravel.
 * Defineix totes les rutes HTTP de l'aplicació SPA.
 *
 * $router és injectat des de bootstrap/app.php
 */

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\SubjectsController;
use App\Http\Middleware\AuthMiddleware;

// ── Auth (públiques — sense middleware) ──────────────────────────────────────
$router->get('/login',          [AuthController::class, 'showLogin'])->name('login');
$router->get('/auth/github',    [AuthController::class, 'redirectToGithub'])->name('auth.github');
$router->get('/auth/callback',  [AuthController::class, 'handleCallback'])->name('auth.callback');
$router->get('/logout',         [AuthController::class, 'logout'])->name('logout');

// ── Middleware: protegir totes les rutes de sota (excepte les públiques) ─────
$_currentUri = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$_publicRoutes = ['/login', '/auth/github', '/auth/callback', '/logout'];
if (!in_array($_currentUri, $_publicRoutes)) {
    AuthMiddleware::handle();
}

// ── Dashboard ────────────────────────────────────────────────────────────────
$router->get('/',          [DashboardController::class, 'index'])->name('dashboard');
$router->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

// ── Students ─────────────────────────────────────────────────────────────────
$router->get('/students',              [StudentsController::class, 'index'])->name('students.index');
$router->get('/students/create',       [StudentsController::class, 'create'])->name('students.create');
$router->post('/students',             [StudentsController::class, 'store'])->name('students.store');
$router->get('/students/{id}/edit',    [StudentsController::class, 'edit'])->name('students.edit');
$router->put('/students/{id}',         [StudentsController::class, 'update'])->name('students.update');
$router->delete('/students/{id}',      [StudentsController::class, 'destroy'])->name('students.destroy');

// ── Teachers ─────────────────────────────────────────────────────────────────
$router->get('/teachers',              [TeachersController::class, 'index'])->name('teachers.index');
$router->get('/teachers/create',       [TeachersController::class, 'create'])->name('teachers.create');
$router->post('/teachers',             [TeachersController::class, 'store'])->name('teachers.store');
$router->get('/teachers/{id}/edit',    [TeachersController::class, 'edit'])->name('teachers.edit');
$router->put('/teachers/{id}',         [TeachersController::class, 'update'])->name('teachers.update');
$router->delete('/teachers/{id}',      [TeachersController::class, 'destroy'])->name('teachers.destroy');

// ── Subjects ─────────────────────────────────────────────────────────────────
$router->get('/subjects',                          [SubjectsController::class, 'index'])->name('subjects.index');
$router->get('/subjects/create',                   [SubjectsController::class, 'create'])->name('subjects.create');
$router->post('/subjects',                         [SubjectsController::class, 'store'])->name('subjects.store');
$router->get('/subjects/{id}/edit',                [SubjectsController::class, 'edit'])->name('subjects.edit');
$router->put('/subjects/{id}',                     [SubjectsController::class, 'update'])->name('subjects.update');
$router->post('/subjects/{id}/assign-teacher',     [SubjectsController::class, 'assignTeacher'])->name('subjects.assign');
$router->delete('/subjects/{id}',                  [SubjectsController::class, 'destroy'])->name('subjects.destroy');
