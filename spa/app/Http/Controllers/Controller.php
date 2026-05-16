<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;

/**
 * BASE CONTROLLER — app/Http/Controllers/Controller.php
 *
 * Equivalent al Controller base de Laravel.
 * Tots els controllers hereten d'aquí.
 */
abstract class Controller
{
    protected ApiClient $api;

    public function __construct()
    {
        $this->api = new ApiClient();
    }

    /**
     * Retorna l'error de l'API si n'hi ha, o null.
     */
    protected function apiError(array $response): ?string
    {
        return $response['error'] ?? null;
    }

    /**
     * Valida que els camps requerits no estiguin buits.
     * Retorna array d'errors o buit si tot és vàlid.
     */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $rule) {
            if ($rule === 'required' && empty(trim($data[$field] ?? ''))) {
                $errors[$field] = "El camp '{$field}' és obligatori.";
            }
            if ($rule === 'email' && !empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "El camp '{$field}' ha de ser un email vàlid.";
            }
        }
        return $errors;
    }

    /**
     * Guarda l'input antic a la sessió per poder mostrar-lo després d'un error.
     */
    protected function flashOldInput(array $data): void
    {
        $_SESSION['_old_input'] = $data;
    }
}
