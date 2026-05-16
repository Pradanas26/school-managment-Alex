<?php

namespace App\Http;

final class Request
{
    private string $method;
    private string $uri;
    private array $body;

    public function __construct()
    {
        $this->method = filter_var($_SERVER['REQUEST_METHOD']);
        $this->uri = parse_url(
            filter_var($_SERVER['REQUEST_URI']),
            PHP_URL_PATH
        );
        $this->body = json_decode(file_get_contents('php://input'), true) ?? [];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getBody(): array
    {
        return $this->body;
    }
}
