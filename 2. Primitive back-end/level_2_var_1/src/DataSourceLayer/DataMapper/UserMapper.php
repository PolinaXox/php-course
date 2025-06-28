<?php

namespace App\DataSourceLayer\DataMapper;

use App\DomainLayer\Entity\User as User;
use App\DomainLayer\Exception\AppException as AppException;

class UserMapper
{
    /**
     * @param User $user
     * @return array
     */
    public function mapToDatabaseRecord(User $user): array
    {
        return $user->toArray();
    }

    /**
     * @param array $databaseRecord
     * @return User
     * @throws AppException
     */
    public function mapToEntity(array $databaseRecord): User
    {
        return new User(
            login: $databaseRecord['login'],
            password: $databaseRecord['password'],
            id: $databaseRecord['id']
        );
    }
}