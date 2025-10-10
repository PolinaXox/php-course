<?php

namespace App\DomainLayer\BusinessService;

use App\DataSourceLayer\DAO\ToDoListDAO as ToDoListDAO;
use App\DataSourceLayer\ServiceDB\FileService as FileService;
use App\DataSourceLayer\ServiceDB\IdCreator;
use App\DomainLayer\Entity\ToDoList as ToDoList;
use App\DomainLayer\Entity\User as User;
use App\DomainLayer\Exception\AppException as AppException;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;

class ToDoListService
{
    private const string FILE_NAME_PREFIX = 'toDoList_';
    private const string FILE_EXTENSION = '.json';

    const string TO_DO_LISTS_DIR = __DIR__ . '/../../../FileDB/toDoLists/';

    /**
     * Uses during registration.
     * Creates record in ToDoList 'table' (file). NOT user's file.
     *
     * @param User $user
     * @return bool
     * @throws AppException
     */
    public function createRecord(User $user): bool
    {
        return new ToDoListDAO()->create(
            new ToDoList(
                id: IdCreator::createNewId(),
                userId: $user->id,
                fileName: $this->getFileNameByUserName($user->login)
            )
        );
    }

    /**
     * @param string $userName
     * @return string
     */
    private function getFileNameByUserName(string $userName): string
    {
        return self::FILE_NAME_PREFIX . $userName . self::FILE_EXTENSION;
    }

    /**
     * @param User $user
     * @return ToDoList
     * @throws AppException
     */
    public function getToDoList(User $user) : ToDoList {

        $toDoList = new ToDoListDAO()->getUserToDoList($user->id);
        $this->createIfNotExists($toDoList->fileName);
        return $toDoList;

    }

    /**
     * Uses during authentication
     *
     * @param string $fileName
     * @throws AppException
     */
    public function createIfNotExists(string $fileName): void
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