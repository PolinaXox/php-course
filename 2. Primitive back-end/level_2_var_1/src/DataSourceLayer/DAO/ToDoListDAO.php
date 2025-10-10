<?php

namespace App\DataSourceLayer\DAO;

use App\DataSourceLayer\DAO\Abstraction\AbstractDAO;
use App\DataSourceLayer\DataMapper\ToDoListMapper as ToDoListMapper;
use App\DomainLayer\Entity\Abstraction\DomainObject;
use App\DomainLayer\Entity\ToDoList as ToDoList;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsEnum as AppExceptionsEnum;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;
use Override as Override;

class ToDoListDAO extends AbstractDAO
{
    private string $filePathValue = __DIR__ . '/../../../FileDB/to_do_lists.json';
    protected string $filePath { #[Override] get => $this->filePathValue; }

    /**
     * @param int $userId
     * @return ToDoList|AbsentValue
     */
    public function findByUser(int $userId): ToDoList|AbsentValue
    {
        foreach ($this->dataSet as $toDoList) {
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
            throw AppException::fromEnum(AppExceptionsEnum::DBRecordNotFound,
                ['ToDoList record for user id ' . $userId . ' NOT found in file ' . $this->filePath]);
        }

        return $toDoList;
    }

    /**
     * @param DomainObject $entity
     * @return int
     * @throws AppException
     */
    #[Override]
    protected function extractKey(DomainObject $entity): int
    {
        if($entity instanceof ToDoList) {
            return $entity->id;
        }

        throw AppException::fromEnum(
            AppExceptionsEnum::InvalidDataType,
            ['expected type'=>'ToDoList', 'given' => get_class($entity)]
        );
    }

    /**
     * @param DomainObject $entity
     * @return array
     * @throws AppException
     */
    #[Override]
    protected function mapToDataBaseRecord(DomainObject $entity): array
    {
        if($entity instanceof ToDoList) {
            return new ToDoListMapper()->mapToDatabaseRecord($entity);
        }

        throw AppException::fromEnum(
            AppExceptionsEnum::InvalidDataType,
            ['expected type'=>'ToDoList', 'given' => get_class($entity)]
        );
    }

    /**
     * @param array $databaseRecord
     * @return ToDoList
     */
    #[Override]
    protected function mapToEntity(array $databaseRecord): ToDoList
    {
        return new ToDoListMapper()->mapToEntity($databaseRecord);
    }
}