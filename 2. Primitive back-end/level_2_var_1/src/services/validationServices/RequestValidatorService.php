<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

class RequestValidatorService
{
    const array REQUEST_METHOD_OPTIONS = ['POST', 'GET', 'PUT', 'DELETE', 'OPTIONS'];
    const string ADDITIONAL_CORS_METHOD = 'OPTIONS';
    const array VALID_REQUIRED_HEADERS_VALUES = [
        'Content-Type' => 'application/json'
    ];

    private readonly RequestDTO $requestToValidate;

    private function __construct(private readonly string $methodNeeded)
    {
        $this->requestToValidate = new RequestDTO();
    }

    /**
     * @param $method
     * @return void
     * @throws Exception
     */
    public static function validate($method): void
    {
        new self($method)->ensureRequestIsValid();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function ensureRequestIsValid(): void
    {
        self::ensureMethodIsValid();
        if($this->methodNeeded !== 'GET') {
            self::ensureRequiredHeadersValid();
            self::ensureJSONFileIsValid();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function ensureMethodIsValid(): void
    {
        $requestMethod = $this->requestToValidate->method;

        if ($requestMethod === $this->methodNeeded) {
            return;
        }

        if ($requestMethod === self::ADDITIONAL_CORS_METHOD) {
            throw new Exception('CORS additional method handling', 200);
        }

        if (in_array($requestMethod, self::REQUEST_METHOD_OPTIONS)) {
            throw new Exception('Method was permitted, but unexpected', 404);
        }

        throw new Exception('Request method is invalid', 400);
    }

    /**
     * @return void
     * @throws Exception
     */
    private function ensureRequiredHeadersValid(): void
    {
        $requiredHeaders = array_keys(self::VALID_REQUIRED_HEADERS_VALUES);
        foreach ($requiredHeaders as $requiredHeader) {
            self::ensureOneHeaderIsValid($requiredHeader);
        }
    }

    /**
     * Can be used as a callback function in array_walk()
     *
     * @param string $requiredHeader
     * @return void
     * @throws Exception
     */
    private function ensureOneHeaderIsValid(string $requiredHeader): void
    {
        if (!str_contains($this->requestToValidate->headers[$requiredHeader] ?? '',
            self::VALID_REQUIRED_HEADERS_VALUES[$requiredHeader])) {
            throw new Exception('Header\'s \'' . $requiredHeader . '\' value is invalid.', 400);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function ensureJSONFileIsValid(): void
    {
        if (!json_validate($this->requestToValidate->body)) {
            throw new Exception('JSON file is invalid: ' . json_last_error_msg(), 400);
        }
    }
}