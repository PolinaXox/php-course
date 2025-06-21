<?php
// ++
namespace App\DataSourceLayer\ServiceDB;

use App\DataSourceLayer\ServiceDB\FileService as FileService;
use App\DomainLayer\Exception\AppException as AppException;

class IdCreator
{
    const string ID_COUNTER_FILE = __DIR__ . '/../../../FileDB/id_counter.txt';

    /**
     * @return int
     * @throws AppException
     */
    // ++
    public static function createNewId(): int
    {
        new FileService()->ensureFileExists(self::ID_COUNTER_FILE);
        $currentId = file_get_contents(self::ID_COUNTER_FILE) + 1;
        file_put_contents(self::ID_COUNTER_FILE, $currentId);

        return $currentId;
    }
}