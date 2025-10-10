<?php

namespace App\PresentationLayer\Request;

use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsEnum as AppExceptionsEnum;
use JsonException;

class JsonDataExtractor
{
    /**
     * @param string ...$requiredFieldsKeys
     * @return array
     * @throws AppException
     */
    public function extract(string ...$requiredFieldsKeys): array
    {
        // залежність від globals
        $this->requireContentType($_SERVER['CONTENT_TYPE'], 'application/json');
        $rawBody = $this->getRawBody();
        $parsedBody = $this->parseBody($rawBody);

        return $requiredFieldsKeys ? $this->extractRequiredFields($parsedBody, $requiredFieldsKeys) : $parsedBody;
    }

    /**
     * @param string|null $requestContentType
     * @param string $requiredContentType
     * @return void
     * @throws AppException
     */
    private function requireContentType(?string $requestContentType, string $requiredContentType): void
    {
        if (!str_contains($requestContentType ?? '', $requiredContentType)) {
            throw AppException::fromEnum(
                AppExceptionsEnum::RequestContentTypeInvalid,
                ['reason' => 'Content-Type must be ' . $requiredContentType],
            );
        }
    }

    /**
     * @return string
     * @throws AppException
     */
    private function getRawBody(): string
    {
        $rawBody = file_get_contents('php://input');
        if (!$rawBody) {
            throw AppException::fromEnum(AppExceptionsEnum::JsonInvalid, ['reason' => 'JSON body not received']);
        }

        return $rawBody;
    }

    /**
     * @param string $rawBody
     * @return array
     * @throws AppException
     */
    private function parseBody(string $rawBody): array
    {
        try {
            $parsedBody = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw AppException::fromEnum(AppExceptionsEnum::JsonInvalid, ['jsonError' => $e->getMessage()]);
        }

        return $parsedBody;
    }

    /**
     * @param array $data
     * @param array $requiredFieldsKeys
     * @return array
     * @throws AppException
     */
    private function extractRequiredFields(array $data, array $requiredFieldsKeys): array
    {
        // чи є щось у масиві?
        if (!$data) {
            throw AppException::fromEnum(AppExceptionsEnum::JsonInvalid, ['reason' => 'JSON body is empty']);
        }

        $requiredData = [];
        // витягування конкретних полів
        foreach ($requiredFieldsKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw AppException::fromEnum(ex: AppExceptionsEnum::RequiredFieldMissing, details: ['reqField' => $key]);
            }

            $requiredData[$key] = $data[$key];
        }

        return $requiredData;
    }
}