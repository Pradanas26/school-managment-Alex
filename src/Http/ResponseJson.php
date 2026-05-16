<?php

namespace App\Http;

class ResponseJson
{
    private int    $statusCode;
    private string $body;

    public function __construct(int $statusCode, array $data)
    {
        $this->statusCode = $statusCode;
        $this->body       = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');
        echo $this->body;
        exit;
    }
}
