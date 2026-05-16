<?php

namespace App\Http\Middleware;

use App\Http\ResponseJson;

/**
 * API AUTH MIDDLEWARE — src/Http/Middleware/ApiAuthMiddleware.php
 *
 * Verifica el Bearer token a les peticions de l'API REST.
 *
 * El token vàlid és el oauth_token guardat a la sessió.
 * Per a clients externs (Postman, tests), s'accepta un API_TOKEN
 * configurat al .env.
 *
 * Ús:
 *   ApiAuthMiddleware::handle();  // atura si no autenticat
 */
class ApiAuthMiddleware
{
    public static function handle(): void
    {
        $token = self::extractToken();

        if (!$token) {
            (new ResponseJson(401, [
                'error' => 'Unauthorized: Bearer token required',
                'hint'  => 'Add header: Authorization: Bearer <token>',
            ]))->send();
            exit;
        }

        if (!self::isValid($token)) {
            (new ResponseJson(401, [
                'error' => 'Unauthorized: Invalid or expired token',
            ]))->send();
            exit;
        }
    }

    /**
     * Extreu el Bearer token de la capçalera Authorization.
     */
    private static function extractToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        // Also accept as query param for easy testing
        return $_GET['api_token'] ?? null;
    }

    /**
     * Valida el token.
     * Accepta:
     *  1. API_TOKEN del .env (per a Postman/tests)
     *  2. oauth_token de la sessió activa (usuari web)
     */
    private static function isValid(string $token): bool
    {
        // 1. Static API token from .env
        $staticToken = $_ENV['API_TOKEN'] ?? getenv('API_TOKEN') ?? '';
        if (!empty($staticToken) && hash_equals($staticToken, $token)) {
            return true;
        }

        // 2. Session-based OAuth token
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $sessionToken = $_SESSION['oauth_token'] ?? '';
        if (!empty($sessionToken) && hash_equals($sessionToken, $token)) {
            return true;
        }

        return false;
    }
}
