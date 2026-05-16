<?php

namespace App\Http\Routing;

use App\Http\Request;
use App\Http\ResponseJson;
use Doctrine\ORM\EntityManagerInterface;

class Router
{
    private RouteCollection $routeCollection;

    function __construct(RouteCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
    }

    function dispatch(Request $request, EntityManagerInterface $em): void
    {
        // ── CORS preflight (OPTIONS) ──────────────────────────────────────
        // Browsers send OPTIONS before PUT/DELETE — respond immediately
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            http_response_code(204);
            $this->sendCorsHeaders();
            exit;
        }

        $routes = $this->routeCollection->getRoutes();
        $pathMatched = false;

        foreach ($routes as $route) {
            if (!$this->matchUri($route['path'], $request->getUri(), $params)) {
                continue;
            }

            $pathMatched = true;

            if ($route['method'] !== strtoupper($request->getMethod())) {
                continue;
            }

            // ── Route found: instantiate controller and call action ───────
            [$controllerClass, $action] = $route['handler'];
            $controller = new $controllerClass($request, $em);
            call_user_func_array([$controller, $action], $params);
            return;
        }

        // ── No match ─────────────────────────────────────────────────────
        if (!$pathMatched) {
            (new ResponseJson(404, [
                'error' => 'Route not found',
                'path'  => $request->getUri(),
            ]))->send();
        } else {
            // Path exists but method not allowed
            (new ResponseJson(405, [
                'error'  => 'Method not allowed',
                'method' => $request->getMethod(),
            ]))->send();
        }
    }

    private function sendCorsHeaders(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');
        header('Access-Control-Max-Age: 86400');
    }

    private function matchUri(string $routePath, string $requestUri, &$params): bool
    {
        // Strip query string from URI
        $uri = strtok($requestUri, '?');

        $pattern = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $routePath);
        $pattern = "#^" . $pattern . "$#";

        if (preg_match($pattern, $uri, $matches)) {
            $params = array_filter($matches, fn($key) => is_string($key), ARRAY_FILTER_USE_KEY);
            return true;
        }
        return false;
    }
}
