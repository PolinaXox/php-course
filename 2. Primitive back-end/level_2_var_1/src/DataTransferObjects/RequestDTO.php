<?php

class RequestDTO
{
    public string $method;
    public array|false $headers;
    public string|false $body;

    public function __construct()
    {
        // case-sensitive?????????
        $this->method = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->headers = getallheaders();                           // get some () headers???
        $this->body = file_get_contents('php://input');
    }
}