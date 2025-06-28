<?php

namespace App\PresentationLayer\InputValidator;

use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsList as AppExceptionsList;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;
use App\PresentationLayer\InputValidator\InputSanitizer as Sanitizer;

class InputValidator
{
    private array $jsonAsArray;

    /**
     * @throws AppException
     */
    public function __construct()
    {
        $jsonContent = file_get_contents('php://input');
        $this->ensureJsonIsValid($jsonContent);
        $this->jsonAsArray = json_decode($jsonContent, true);
    }

    /**
     * @param string $inputFile
     * @return void
     * @throws AppException
     */
    private function ensureJsonIsValid(string $inputFile): void
    {
        if (!json_validate($inputFile)) {
            throw AppException::fromEnum(AppExceptionsList::JsonInvalid, ['jsonError' => json_last_error_msg()]);
        }
    }

    /**
     * @param string $key
     * @return string|AbsentValue
     */
    public function getValidValue(string $key): string|AbsentValue
    {
        if (!array_key_exists($key, $this->jsonAsArray)) {
            return AbsentValue::instance();
        }

        return Sanitizer::getSanitizedValue($this->jsonAsArray[$key]);
    }

    /**
     * @param string $key
     * @return string
     * @throws AppException
     */
    public function getRequiredValidValue(string $key): string
    {
        if (!array_key_exists($key, $this->jsonAsArray)) {
            throw AppException::fromEnum(ex: AppExceptionsList::RequiredFieldMissing, details: ['reqField' => $key]);
        }

        $validValue = Sanitizer::getSanitizedValue($this->jsonAsArray[$key]);
        if ($validValue instanceof AbsentValue) {
            throw AppException::fromEnum(AppExceptionsList::RequiredFieldEmpty, ['emptyField' => $key]);
        }

        return $validValue;
    }
}