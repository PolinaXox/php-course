<?php
//+
namespace App\PresentationLayer\DataTransferObjects;

class RequestDTO
{
    public string $method;
    public array|false $headers;
    public string|false $body;

    // todo: прийняти усі поля вне залежності від методу запиту, перетворити на те, що потрібно для подальшої обробки

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? '';     // case-sensitive?????????
        $this->headers = getallheaders();
        $this->body = file_get_contents('php://input');
    }
}