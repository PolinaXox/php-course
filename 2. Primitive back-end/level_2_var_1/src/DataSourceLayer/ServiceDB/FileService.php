<?php

namespace App\DataSourceLayer\ServiceDB;

use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsEnum as AppExceptionsEnum;

class FileService
{
    /**
     * @param string $filePath
     * @return void
     * @throws AppException
     */
    public function ensureFileExists(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw AppException::fromEnum(AppExceptionsEnum::FileNotExists, ['filePath' => $filePath]);
        }
    }
}