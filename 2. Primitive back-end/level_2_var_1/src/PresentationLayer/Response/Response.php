<?php

namespace App\PresentationLayer\Response;

use App\DomainLayer\Exception\AppException;
use Throwable;

readonly class Response
{
    /**
     * @param array $body
     * @param int $statusCode
     * @param string $contentType
     */
    public function __construct(
        private array  $body = [],
        private int    $statusCode = 200,
        private string $contentType = 'application/json'
    ) {}

    /**
     * @param array $data
     * @return self
     */
    public static function success(array $data = []): self
    {
        return new self($data);
    }

    /**
     * @return self
     */
    public static function corsPreflight(): self
    {
        return new self(statusCode: 204);
    }

    /**
     * @param AppException $e
     * @return self
     */
    public static function fromException(AppException $e): self
    {
        $messages = [
            'userMessage' => $e->userMessage,
            'frontDevMessage' => $e->frontDevMessage,
        ];

        return new self(body: $messages, statusCode: $e->getCode());
    }

    /**
     * @param Throwable $t
     * @return self
     */
    public static function fromThrowable(Throwable $t): self
    {
        return new self(body: ['error' => $t->getMessage()], statusCode: 500);
    }

    /**
     * @return void
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: ' . $this->contentType, false);
        echo $this->toJson();
    }

    /**
     * @return string
     */
    private function toJson(): string
    {
        return json_encode($this->body);
    }

    /**
     * @return void
     */
    public function sendNoContent(): void
    {
        http_response_code(204);
    }

    //    public static function fromExceptionAll(AppException $e): self
//    {
//        $messages = [
//            'httpStatusCode' => $e->httpStatusCode,
//            'httpStatusMessage' => $e->httpStatusMessage,
//            'userMessage' => $e->userMessage,
//            'frontDevMessage' => $e->frontDevMessage,
//            'backDevMessage' => $e->backDevMessage,
//        ];
//
//        return new self(
//            body: $messages,
//            statusCode: $e->getCode(),
//        );
//    }
}
