<?php

namespace App\Http\Controllers;

/**
 * AUTH CONTROLLER — spa/app/Http/Controllers/AuthController.php
 *
 * Gestiona el flux OAuth 2.0 amb GitHub.
 *
 * Flux:
 *   1. GET /login           → Mostra pàgina de login
 *   2. GET /auth/github     → Redirigeix a GitHub OAuth
 *   3. GET /auth/callback   → GitHub retorna amb ?code=...
 *   4. GET /logout          → Tanca sessió
 *
 * Variables d'entorn necessàries (.env):
 *   GITHUB_CLIENT_ID=...
 *   GITHUB_CLIENT_SECRET=...
 *   APP_URL=http://localhost:8000
 */
class AuthController
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct()
    {
        $this->clientId     = $_ENV['GITHUB_CLIENT_ID']     ?? getenv('GITHUB_CLIENT_ID')     ?? '';
        $this->clientSecret = $_ENV['GITHUB_CLIENT_SECRET'] ?? getenv('GITHUB_CLIENT_SECRET') ?? '';
        $appUrl             = $_ENV['APP_URL']               ?? getenv('APP_URL')               ?? 'http://localhost:8000';
        $this->redirectUri  = rtrim($appUrl, '/') . '/auth/callback';
    }

    /**
     * GET /login — Mostra la pàgina de login
     */
    public function showLogin(): void
    {
        if (\App\Http\Middleware\AuthMiddleware::check()) {
            redirect('/');
        }

        $error = $_GET['error'] ?? null;

        view('auth.login', [
            'pageTitle'   => 'Iniciar sessió',
            'error'       => $error,
            'hasConfig'   => !empty($this->clientId) && !empty($this->clientSecret),
            'githubAuthUrl' => $this->buildGithubUrl(),
        ]);
    }

    /**
     * GET /auth/github — Inicia el flux OAuth amb GitHub
     */
    public function redirectToGithub(): void
    {
        if (empty($this->clientId)) {
            redirect('/login?error=no_config');
        }

        session_start_if_not_started();

        // CSRF state token
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        $url = $this->buildGithubUrl($state);
        redirect($url);
    }

    /**
     * GET /auth/callback — GitHub retorna aquí amb el codi
     */
    public function handleCallback(): void
    {
        session_start_if_not_started();

        // Errors de GitHub
        if (!empty($_GET['error'])) {
            redirect('/login?error=' . urlencode($_GET['error_description'] ?? $_GET['error']));
        }

        $code  = $_GET['code']  ?? '';
        $state = $_GET['state'] ?? '';

        // Verificar CSRF state
        $savedState = $_SESSION['oauth_state'] ?? '';
        unset($_SESSION['oauth_state']);

        if (empty($code) || $state !== $savedState) {
            redirect('/login?error=invalid_state');
        }

        // Intercanviar codi per token
        $token = $this->exchangeCodeForToken($code);
        if (!$token) {
            redirect('/login?error=token_exchange_failed');
        }

        // Obtenir dades de l'usuari
        $user = $this->fetchGithubUser($token);
        if (!$user) {
            redirect('/login?error=user_fetch_failed');
        }

        // Guardar a sessió
        $_SESSION['oauth_user'] = [
            'id'         => $user['id'],
            'name'       => $user['name'] ?? $user['login'],
            'login'      => $user['login'],
            'email'      => $user['email'] ?? '',
            'avatar_url' => $user['avatar_url'] ?? '',
            'provider'   => 'github',
        ];
        $_SESSION['oauth_token'] = $token;

        // Redirigir a la URL original o al dashboard
        $intended = $_SESSION['intended_url'] ?? '/';
        unset($_SESSION['intended_url']);

        // Avoid redirect loops
        if ($intended === '/login' || str_starts_with($intended, '/auth')) {
            $intended = '/';
        }

        redirect($intended);
    }

    /**
     * GET /logout — Tanca la sessió
     */
    public function logout(): void
    {
        session_start_if_not_started();
        session_destroy();
        redirect('/login');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function buildGithubUrl(string $state = ''): string
    {
        if (empty($this->clientId)) {
            return '#';
        }
        $params = http_build_query([
            'client_id'    => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope'        => 'read:user user:email',
            'state'        => $state ?: bin2hex(random_bytes(8)),
        ]);
        return 'https://github.com/login/oauth/authorize?' . $params;
    }

    private function exchangeCodeForToken(string $code): ?string
    {
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => implode("\r\n", [
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'User-Agent: SchoolProject/1.0',
                ]),
                'content' => http_build_query([
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code'          => $code,
                    'redirect_uri'  => $this->redirectUri,
                ]),
                'timeout' => 10,
            ]
        ]);

        $response = @file_get_contents('https://github.com/login/oauth/access_token', false, $context);
        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    private function fetchGithubUser(string $token): ?array
    {
        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => implode("\r\n", [
                    'Authorization: Bearer ' . $token,
                    'Accept: application/vnd.github+json',
                    'User-Agent: SchoolProject/1.0',
                ]),
                'timeout' => 10,
            ]
        ]);

        $response = @file_get_contents('https://api.github.com/user', false, $context);
        if (!$response) {
            return null;
        }

        return json_decode($response, true);
    }
}
