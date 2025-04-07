<?php

class FileService
{
    const string TO_DO_LISTS_DIR = __DIR__ . '/../../FileDB/toDoLists/';

    /**
     * @param User $user
     * @return void
     * @throws Exception
     */
    public static function ensureUserFileExistOrCreate(User $user): void
    {
        $fileName = ToDoListDAO::getUserToDoListFileName($user);
        $filePath = self::TO_DO_LISTS_DIR . $fileName;
        if (file_exists($filePath)) {
            return;
        }
        self::createToDoListFilePhysically($filePath);
        self::ensureFileExists($filePath);
    }

    /**
     * @param string $filePath
     * @return void
     */
    private static function createToDoListFilePhysically(string $filePath): void
    {
        file_put_contents($filePath, json_encode(['items' => []]));
    }

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
     * @param string $fileName
     * @return array
     * @throws Exception
     */
    public static function getToDoListFileContentAsArray(string $fileName): array
    {
        return json_decode(self::getToDoListFileContent($fileName), true)['items'];
    }

    /**
     * @param string $fileName
     * @return false|string
     * @throws Exception
     */
    public static function getToDoListFileContent(string $fileName): false|string
    {
        $filePath = self::getUserToDoListFilePath($fileName);
        self::ensureFileExists($filePath);
        return file_get_contents($filePath);
    }

    /**
     * @param string $fileName
     * @return false|string
     */
    private static function getUserToDoListFilePath(string $fileName): false|string
    {
        return realpath(self::TO_DO_LISTS_DIR . $fileName);
    }

    /**
     * @param string $fileName
     * @param array $newContent
     * @return bool
     */
    public static function rewriteFile(string $fileName, array $newContent): bool
    {
        return file_put_contents(
            self::getUserToDoListFilePath($fileName),
            json_encode(['items' => $newContent])
        );
    }
}