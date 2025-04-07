<?php

class RequestDTO
{
    public string $method;
    public array|false $headers;
    public string|false $body;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? '';     // case-sensitive?????????
        $this->headers = getallheaders();
        $this->body = file_get_contents('php://input');
    }
}