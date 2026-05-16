<?php

namespace App\Http\Middleware;

/**
 * AUTH MIDDLEWARE — spa/app/Http/Middleware/AuthMiddleware.php
 *
 * Protegeix les rutes del SPA. Si l'usuari no és autenticat,
 * redirigeix a /login.
 */
class AuthMiddleware
{
    public static function handle(): void
    {
        session_start_if_not_started();

        if (empty($_SESSION['oauth_user'])) {
            // Save intended URL
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? '/';
            redirect('/login');
        }
    }

    public static function user(): ?array
    {
        session_start_if_not_started();
        return $_SESSION['oauth_user'] ?? null;
    }

    public static function check(): bool
    {
        session_start_if_not_started();
        return !empty($_SESSION['oauth_user']);
    }
}
