<?php

namespace App\DataSourceLayer\DAO;

use App\DataSourceLayer\DAO\Abstraction\AbstractDAO;
use App\DataSourceLayer\DataMapper\UserMapper as UserMapper;
use App\DomainLayer\Entity\Abstraction\DomainObject;
use App\DomainLayer\Entity\User as User;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsEnum as AppExceptionsEnum;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;
use Override as Override;

class UserDAO extends AbstractDAO
{
    private string $filePathValue = __DIR__ . '/../../../FileDB/registered_users.json';

    protected string $filePath { #[Override] get => $this->filePathValue; }

    /**
     * @param string $login
     * @return User|AbsentValue
     */
    public function findByLogin(string $login): User|AbsentValue
    {
        return $this->findByKey($login);
    }

    /**
     * @param DomainObject $entity
     * @return string
     * @throws AppException
     */
    #[Override]
    protected function extractKey(DomainObject $entity) : string
    {
        if($entity instanceof User) {
            return $entity->login;
        }

        throw AppException::fromEnum(
            AppExceptionsEnum::InvalidDataType,
            ['expected type'=>'User', 'given' => get_class($entity)]
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
        if($entity instanceof User) {
            return new UserMapper()->mapToDatabaseRecord($entity);
        }

        throw AppException::fromEnum(
            AppExceptionsEnum::InvalidDataType,
            ['expected type'=>'User', 'given' => get_class($entity)]
        );
    }

    /**
     * @param array $databaseRecord
     * @return User
     */
    #[Override]
    protected function mapToEntity(array $databaseRecord): User
    {
        return new UserMapper()->mapToEntity($databaseRecord);
    }

}

