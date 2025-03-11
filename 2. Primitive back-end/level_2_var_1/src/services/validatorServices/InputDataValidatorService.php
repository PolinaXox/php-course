<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use SanitizerService as Sanitizer;

abstract class InputDataValidatorService
{
    protected function __construct(
        private readonly array $inputRawData
    )
    {
    }

    /**
     * @param string $key
     * @return string
     * @throws Exception
     */
    protected function getValidValue(string $key): string
    {
        $this->ensureValueExists($key);

        return $this->getSanitizedValue($key);
    }

    /**
     * @param string $key
     * @return void
     * @throws Exception
     */
    private function ensureValueExists(string $key): void
    {
        if ($this->inputRawData[$key] === null)
            throw new Exception('Request does NOT contain \'' . $key . '\' field.', 400);
    }

    /**
     * @param string $key
     * @return string
     * @throws Exception
     */
    private function getSanitizedValue(string $key): string
    {
        if (empty($sanitizedValue = Sanitizer::sanitizeInputValue($this->inputRawData[$key])))
            throw new Exception('Request field \'' . $key . '\' is empty or contains spaces only', 400);

        return $sanitizedValue;
    }
}