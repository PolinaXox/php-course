<?php

namespace App\DomainLayer\BusinessService;

use App\DataSourceLayer\DAO\ToDoListDAO as ToDoListDAO;
use App\DataSourceLayer\ServiceDB\FileService as FileService;
use App\DomainLayer\Entity\ToDoList as ToDoList;
use App\DomainLayer\Entity\User as User;
use App\DomainLayer\Exception\AppException as AppException;

class ToDoListCreationService
{
    private const string FILE_NAME_PREFIX = 'toDoList_';
    private const string FILE_EXTENSION = '.json';

    const string TO_DO_LISTS_DIR = __DIR__ . '/../../../FileDB/toDoLists/';

    /**
     * Uses during registration.
     * Creates record in ToDoList 'table' (file)
     *
     * @param User $user
     * @return bool
     * @throws AppException
     */
    public function create(User $user): bool
    {
        $fileName = $this->createFileName($user->login);

        return new ToDoListDAO()->save(ToDoList::createNewToDoList(userId: $user->id, fileName: $fileName));
    }

    /**
     * @param string $userName
     * @return string
     */
    private function createFileName(string $userName): string
    {
        return self::FILE_NAME_PREFIX . $userName . self::FILE_EXTENSION;
    }


    /**
     * Uses during authentication
     *
     * @param string $fileName
     * @throws AppException
     */
    public function ensureFileExistOrCreate(string $fileName): void
    {
        $filePath = self::TO_DO_LISTS_DIR . $fileName;

        if (file_exists($filePath)) {
            return;
        }

        // empty file creation
        file_put_contents($filePath, null);
        new FileService()->ensureFileExists($filePath);
    }
}