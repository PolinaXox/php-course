<?php
// ++
namespace App\DataSourceLayer\ServiceDB;

use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsList as AppExceptionsList;

class FileService
{
    /**
     * @param string $filePath
     * @return void
     * @throws AppException
     */
    // ++
    public function ensureFileExists(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw AppException::fromEnum(AppExceptionsList::FileNotExist, ['filePath' => $filePath]);
        }
    }
}