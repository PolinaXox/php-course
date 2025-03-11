<?php

class FileService
{
    /**
     * @param string $filePath
     * @return void
     * @throws Exception
     */
    public static function ensureFileExists(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new Exception('File \'' . basename($filePath) . '\' does NOT exist', 500);
        }
    }


    /**
     * @param string $filePath
     * @return void
     * @throws Exception
     */
    public static function createToDoListFilePhysically(string $filePath): void
    {
        if(!file_put_contents($filePath, json_encode(['items' => []]))) {
            throw new Exception('File \'' . basename($filePath) . '\' does NOT exist', 500);
        }
    }

}