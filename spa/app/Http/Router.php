<?php

namespace App\Http;

/**
 * ROUTER — app/Http/Router.php
 *
 * Router HTTP equivalent al de Laravel.
 * Suporta:  GET, POST, PUT, DELETE, PATCH
 * Wildcards: {id}, {slug}, etc.
 * Named routes: ->name('students.index')
 */
class Router
{
    private static array $named = [];
    private array $routes = [];

    // ── Route registration ────────────────────────────────────────────────

    public function get(string $uri, array|callable $handler): self
    {
        return $this->add('GET', $uri, $handler);
    }

    public function post(string $uri, array|callable $handler): self
    {
        return $this->add('POST', $uri, $handler);
    }

    public function put(string $uri, array|callable $handler): self
    {
        return $this->add('PUT', $uri, $handler);
    }

    public function delete(string $uri, array|callable $handler): self
    {
        return $this->add('DELETE', $uri, $handler);
    }

    public function name(string $name): self
    {
        // Name the last registered route
        $last = end($this->routes);
        self::$named[$name] = $last['uri'];
        return $this;
    }

    public static function namedRoutes(): array
    {
        return self::$named;
    }

    // ── Dispatch ──────────────────────────────────────────────────────────

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri    = '/' . trim($uri, '/');

        // Method override (_method hidden input or X-HTTP-Method-Override header)
        if ($method === 'POST') {
            $override = $_POST['_method'] ?? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? null;
            if ($override && in_array(strtoupper($override), ['PUT', 'PATCH', 'DELETE'])) {
                $method = strtoupper($override);
            }
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['uri'], $uri);
            if ($params !== false) {
                $this->call($route['handler'], $params);
                return;
            }
        }

        // 404
        http_response_code(404);
        view('errors.404');
    }

    // ── Private ───────────────────────────────────────────────────────────

    private function add(string $method, string $uri, array|callable $handler): self
    {
        $this->routes[] = compact('method', 'uri', 'handler');
        return $this;
    }

    private function match(string $routeUri, string $requestUri): array|false
    {
        // Convert {param} to named capture groups
        $pattern = preg_replace('/\{([a-z_]+)\}/', '(?P<$1>[^/]+)', $routeUri);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestUri, $matches)) {
            // Return only named captures
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return false;
    }

    private function call(array|callable $handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();
        call_user_func_array([$controller, $method], $params);
    }
}
