<?php

namespace App\PresentationLayer\InputValidator;

use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsEnum as AppExceptionsEnum;
use App\PresentationLayer\InputValidator\InputSanitizer as Sanitizer;

class InputValidator
{
    private readonly array $ruleHandlers;
    private(set) array $validatedData = [];

    /**
     * @param array $data
     * @param array $fieldsAndRules
     * @param array $checkers
     * @param array $exceptions
     */
    public function __construct(
        private readonly array $data,
        private readonly array $fieldsAndRules,
        private readonly array $checkers = [],
        private readonly array $exceptions = [],

    )
    {
        $this->ruleHandlers = [     // mb separate obj???
            'checkType' => fn($field, $val) => $this->assertType($field, $val),
            'notEmpty' => fn($field, $val) => $this->assertNotEmpty($field, $val),
            'unique' => fn($field, $val) => $this->assertUniqueness($field, $val),
            'exists' => fn($field, $val) => $this->assertExistence($field, $val),

        ];
    }

    /**
     * @return self
     * @throws AppException
     */
    public function validate(): self
    {
        foreach ($this->fieldsAndRules as $field => $rules) {

            // if input value exists...
            if (!array_key_exists($field, $this->data)) {
                throw AppException::fromEnum(
                    ex: AppExceptionsEnum::RequiredFieldMissing, details: ['reqField' => $field]);
            }

            // ... sanitize it and ...
            $sanitizedValue = new Sanitizer()->getSanitizedValue($this->data[$field]);

            // ... apply all the rules
            foreach ($rules as $rule) {
                $this->ruleHandlers[$rule]($field, $sanitizedValue);
            }

            $this->validatedData[$field] = $sanitizedValue;
        }

        return $this;
    }

    /**
     * @param mixed $value
     * @param string $field
     * @return void
     * @throws AppException
     */
    private function assertType(string $field, mixed $value): void
    {
        $key = $field . ':checkType';
        $checker = $this->checkers[$key] ?? null;

        if (!$checker || $checker($value)) {
            return;
        }

        throw AppException::fromEnum(
            ex: $this->exceptions[$key] ?? AppExceptionsEnum::InvalidInputDataType,
            details: [
                'field' => $field,
                'given type' => is_object($value) ? get_class($value) : gettype($value),
            ]);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return void
     * @throws AppException
     */
    private function assertNotEmpty(string $field, mixed $value): void
    {
        if (!empty($value)) {
            return;
        }

        throw AppException::fromEnum(AppExceptionsEnum::RequiredFieldEmpty, ['emptyField' => $field]);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return void
     * @throws AppException
     */
    private function assertUniqueness(string $field, mixed $value): void
    {
        $key = $field . ':unique';
        $checker = $this->checkers[$key] ?? null;

        if (!$checker || $checker($value)) {
            return;
        }

        throw AppException::fromEnum(
            ex: $this->exceptions[$key] ?? AppExceptionsEnum::FieldValueIsNotUnique, details: ['field' => $field]);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return void
     * @throws AppException
     */
    private function assertExistence(string $field, mixed $value): void
    {
        $key = $field . ':exists';
        $checker = $this->checkers[$key] ?? null;

        if (!$checker || $checker($value)) {
            return;
        }

        throw AppException::fromEnum(
            ex: $this->exceptions[$key] ??
                AppExceptionsEnum::NotExistsInDatabase, details: ['field' => $field, 'validation']);
    }
}