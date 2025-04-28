<?php
namespace App\DataSourceLayer\servicesDB;

use Exception;

class ID_Creator
{
    const string ID_COUNTER_FILE = __DIR__ . '/../../FileDB/id_counter.txt';

    /**
     * @return int
     * @throws Exception
     */
    public static function createNewId(): int
    {
        FileService::ensureFileExists(self::ID_COUNTER_FILE);
        $currentId = file_get_contents(self::ID_COUNTER_FILE) + 1;
        file_put_contents(self::ID_COUNTER_FILE, $currentId);

        return $currentId;
    }
}