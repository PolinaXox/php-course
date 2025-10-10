<?php

namespace App\DataSourceLayer\DAO\Abstraction;

use App\DataSourceLayer\ServiceDB\FileService as FileService;
use App\DomainLayer\Entity\Abstraction\DomainObject as DomainObject;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsEnum as AppExceptionsEnum;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;

abstract class AbstractDAO
{
    protected(set) array $dataSet = [];
    abstract protected string $filePath { get; }

    /**
     * @throws AppException
     */
    public function __construct()
    {
        new FileService()->ensureFileExists($this->filePath);
        $this->dataSet = json_decode(file_get_contents($this->filePath), true) ?? [];
    }

    /**
     * @param DomainObject $entity
     * @return bool
     * @throws AppException
     */
    public function create(DomainObject $entity): bool
    {
        $key = $this->extractKey($entity);

        return ($this->findByKey($key) instanceof AbsentValue) ?
            $this->save($key, $entity) :
            throw AppException::fromEnum(ex: AppExceptionsEnum::EntityAlreadyExistsInDB);
    }

    /**
     * @param DomainObject $entity
     * @return bool
     * @throws AppException
     */
    public function update(DomainObject $entity): bool
    {
        $key = $this->extractKey($entity);

        return $this->findByKey($key) instanceof DomainObject ?
            $this->save($key, $entity) :
            throw AppException::fromEnum( AppExceptionsEnum::NotExistsInDatabase, ['update']);
    }

    /**
     * @param DomainObject $entity
     * @return bool
     * @throws AppException
     */
    public function delete(DomainObject $entity): bool {
        $key = $this->extractKey($entity);

        if($this->findByKey($key) instanceof AbsentValue) {
            throw AppException::fromEnum(AppExceptionsEnum::NotExistsInDatabase, ['delete']);
        }

        unset($this->dataSet[$key]);

        return $this->saveChangesToDB() ?:
            throw AppException::fromEnum(AppExceptionsEnum::PersistenceException, ['DB delete failed']);
    }

    /**
     * @param int|string $key
     * @return DomainObject|AbsentValue
     */
    public function findByKey(int|string $key): DomainObject|AbsentValue
    {
        return array_key_exists($key, $this->dataSet) ?
            $this->mapToEntity($this->dataSet[$key]) :
            AbsentValue::instance();
    }

    /**
     * @param int|string $key
     * @param DomainObject $entity
     * @return bool
     * @throws AppException
     */
    protected function save(int|string $key, DomainObject $entity): bool
    {
        $this->dataSet[$key] = $this->mapToDataBaseRecord($entity);

        return $this->saveChangesToDB();
    }

    /**
     * @return bool
     * @throws AppException
     */
    protected function saveChangesToDB(): bool
    {
        $success = file_put_contents(
            $this->filePath,
            json_encode($this->dataSet, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK)
        );

        return $success ?:
            throw AppException::fromEnum(AppExceptionsEnum::PersistenceException);
    }

    abstract protected function extractKey(DomainObject $entity): int|string;
    abstract protected function mapToEntity(array $databaseRecord): DomainObject;
    abstract protected function mapToDataBaseRecord(DomainObject $entity): array;
}