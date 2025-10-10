<?php

namespace App\DataSourceLayer\DAO;

use App\DataSourceLayer\DAO\Abstraction\AbstractDAO;
use App\DataSourceLayer\DataMapper\ToDoTaskMapper as ToDoTaskMapper;
use App\DomainLayer\Entity\Abstraction\DomainObject;
use App\DomainLayer\Entity\ToDoTask as ToDoTask;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsEnum as AppExceptionsEnum;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;
use Override as Override;

class ToDoTaskDAO extends AbstractDAO
{
    private const string TO_DO_LISTS_DIR = __DIR__ . '/../../../FileDB/toDoLists/';
    private string $filePathValue;
    protected string $filePath { #[Override] get => $this->filePathValue; }


    /**
     * @throws AppException
     */
    public function __construct(string $fileName)
    {
        $this->filePathValue = self::TO_DO_LISTS_DIR . $fileName;
        parent::__construct();
    }

    /**
     * @param DomainObject $entity
     * @return int
     * @throws AppException
     */
    #[Override]
    protected function extractKey(DomainObject $entity): int
    {
        if ($entity instanceof ToDoTask) {
            return $entity->id;
        }

        throw AppException::fromEnum(
            AppExceptionsEnum::InvalidDataType,
            ['expected type'=>'ToDoTask', 'given' => get_class($entity)]
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
        if ($entity instanceof ToDoTask) {
            return new ToDoTaskMapper()->mapToDatabaseRecord($entity);
        }

        throw AppException::fromEnum(
            AppExceptionsEnum::InvalidDataType,
            ['expected type'=>'ToDoTask', 'given' => get_class($entity)]
        );
    }

    /**
     * @param array $databaseRecord
     * @return ToDoTask
     * @throws AppException
     */
    #[Override]
    protected function mapToEntity(array $databaseRecord): ToDoTask
    {
        return new ToDoTaskMapper()->mapToEntity($databaseRecord);

    }

    #[Override]
    public function findByKey(int|string $key): ToDoTask|AbsentValue
    {
        return parent::findByKey($key);
    }
}
