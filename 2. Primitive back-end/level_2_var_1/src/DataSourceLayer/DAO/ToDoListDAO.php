<?php

namespace App\DataSourceLayer\DAO;

use App\DataSourceLayer\DataMapper\ToDoListMapper as ToDoListMapper;
use App\DataSourceLayer\ServiceDB\FileService as FileService;
use App\DomainLayer\BusinessService\ToDoListCreationService as ToDoListCreationService;
use App\DomainLayer\Entity\ToDoList as ToDoList;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsList as AppExceptionsList;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;

class ToDoListDAO
{
    private string $filePath = __DIR__ . '/../../../FileDB/to_do_lists.json';
    private array $toDoLists;

    /**
     * @throws AppException
     */
    public function __construct()
    {
        new FileService()->ensureFileExists($this->filePath);
        $this->toDoLists = json_decode(file_get_contents($this->filePath), true) ?? [];
    }

    /**
     * @param ToDoList $toDoList
     * @return bool
     */
    public function save(ToDoList $toDoList): bool
    {
        $key = $toDoList->id;
        $this->toDoLists[$key] = new ToDoListMapper()->mapToDatabaseRecord($toDoList);

        return $this->saveChangesToDB();
    }

    /**
     * @return bool
     */
    private function saveChangesToDB(): bool
    {
        return file_put_contents(
            $this->filePath,
            json_encode($this->toDoLists, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK)
        );
    }

    /**
     * @param int $userId
     * @return ToDoList|false
     * @throws AppException
     */
    public function findByUser(int $userId): ToDoList|AbsentValue
    {
        foreach ($this->toDoLists as $toDoList) {
            if ($toDoList['userId'] === $userId) {
                return new ToDoListMapper()->mapToEntity($toDoList);
            }
        }

        return AbsentValue::instance();
    }

    /**
     * @param int $userId
     * @return ToDoList
     * @throws AppException
     */
    public function getUserToDoList(int $userId): ToDoList
    {
        $toDoList = $this->findByUser($userId); // mb AbsentValue

        if($toDoList instanceof AbsentValue) {
            throw AppException::fromEnum(AppExceptionsList::DBRecordNotFound,
                ['filePath' => $this->filePath, 'forUserId' => $userId]);
        }

        new ToDoListCreationService()->ensureFileExistOrCreate($toDoList->fileName); // getFileName());

        return $toDoList;
    }
}