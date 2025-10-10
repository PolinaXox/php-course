<?php

namespace App\DataSourceLayer\DataMapper;

use App\DomainLayer\Entity\User as User;

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
     */
    public function mapToEntity(array $databaseRecord): User
    {
        return new User(
            id: $databaseRecord['id'],
            login: $databaseRecord['login'],
            password: $databaseRecord['password']
        );
    }
}