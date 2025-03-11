<?php

require_once (__DIR__ . '/../../../vendor/autoload.php');

class RequestValidatorService
{
    const string VALID_REQUEST_METHOD = 'POST';

    const array VALID_REQUIRED_HEADERS_VALUES = [
        'Content-Type' => 'application/json'
    ];

    private RequestDTO $requestToValidate;

    public function __construct()
    {
        $this->requestToValidate = new RequestDTO();
    }

    /**
     * @return void
     * @throws Exception
     */
    public static function validate(): void
    {
        new self()->ensureRequestIsValid();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function ensureRequestIsValid(): void
    {
        self::ensureMethodIsValid();
        self::ensureRequiredHeadersValid();
        self::ensureJSONFileIsValid();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function ensureMethodIsValid(): void
    {
        if ($this->requestToValidate->method != self::VALID_REQUEST_METHOD) {
            throw new Exception('Request method is invalid.'
                . 'The method ' . self::VALID_REQUEST_METHOD . ' is needed.', 400);
        }
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
     * @return void
     * @throws Exception
     */
    private function ensureJSONFileIsValid(): void
    {
        if (!json_validate($this->requestToValidate->body)) {
            throw new Exception('JSON file is invalid: ' . json_last_error_msg(), 400);
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
}