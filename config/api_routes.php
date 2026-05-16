<?php

use App\Http\Controllers\StudentsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\CoursesController;
return [
    // ── STUDENTS ──────────────────────────────────────────────────────────
    [
        'method'  => 'GET',
        'path'    => '/api/students',
        'handler' => [StudentsController::class, 'index'],
    ],
    [
        'method'  => 'GET',
        'path'    => '/api/students/{id}',
        'handler' => [StudentsController::class, 'show'],
    ],
    [
        'method'  => 'POST',
        'path'    => '/api/students',
        'handler' => [StudentsController::class, 'create'],
    ],
    [
        'method'  => 'PUT',
        'path'    => '/api/students/{id}',
        'handler' => [StudentsController::class, 'update'],
    ],
    [
        'method'  => 'DELETE',
        'path'    => '/api/students/{id}',
        'handler' => [StudentsController::class, 'delete'],
    ],

    // ── TEACHERS ──────────────────────────────────────────────────────────
    [
        'method'  => 'GET',
        'path'    => '/api/teachers',
        'handler' => [TeachersController::class, 'index'],
    ],
    [
        'method'  => 'GET',
        'path'    => '/api/teachers/{id}',
        'handler' => [TeachersController::class, 'show'],
    ],
    [
        'method'  => 'POST',
        'path'    => '/api/teachers',
        'handler' => [TeachersController::class, 'create'],
    ],
    [
        'method'  => 'PUT',
        'path'    => '/api/teachers/{id}',
        'handler' => [TeachersController::class, 'update'],
    ],
    [
        'method'  => 'DELETE',
        'path'    => '/api/teachers/{id}',
        'handler' => [TeachersController::class, 'delete'],
    ],

    // ── SUBJECTS ──────────────────────────────────────────────────────────
    [
        'method'  => 'GET',
        'path'    => '/api/subjects',
        'handler' => [SubjectsController::class, 'index'],
    ],
    [
        'method'  => 'GET',
        'path'    => '/api/subjects/{id}',
        'handler' => [SubjectsController::class, 'show'],
    ],
    [
        'method'  => 'POST',
        'path'    => '/api/subjects',
        'handler' => [SubjectsController::class, 'create'],
    ],
    [
        'method'  => 'PUT',
        'path'    => '/api/subjects/{id}',
        'handler' => [SubjectsController::class, 'update'],
    ],
    [
        'method'  => 'POST',
        'path'    => '/api/subjects/{id}/assign-teacher',
        'handler' => [SubjectsController::class, 'assignTeacher'],
    ],
    [
        'method'  => 'DELETE',
        'path'    => '/api/subjects/{id}',
        'handler' => [SubjectsController::class, 'delete'],
    ],
    // ── COURSES ──────────────────────────────────────────────────────────
    [
        'method'  => 'GET',
        'path'    => '/api/courses',
        'handler' => [CoursesController::class, 'index'],
    ],
    [
        'method'  => 'GET',
        'path'    => '/api/courses/{id}',
        'handler' => [CoursesController::class, 'show'],
    ],
    [
        'method'  => 'POST',
        'path'    => '/api/courses',
        'handler' => [CoursesController::class, 'create'],
    ],
    [
        'method'  => 'DELETE',
        'path'    => '/api/courses/{id}',
        'handler' => [CoursesController::class, 'delete'],
    ],
];
