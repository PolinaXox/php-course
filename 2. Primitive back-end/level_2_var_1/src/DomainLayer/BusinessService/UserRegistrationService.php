<?php

namespace App\DomainLayer\BusinessService;

use App\DataSourceLayer\DAO\UserDAO as UserDAO;
use App\DomainLayer\BusinessService\ToDoListCreationService as ToDoListCreationService;
use App\DomainLayer\Entity\User as User;
use App\DomainLayer\Exception\AppException as AppException;

class UserRegistrationService
{
    /**
     * @param User $user
     * @return void
     * @throws AppException
     */
    // ++
    public function register(User $user): void
    {
        new UserDAO()->save($user);
        new ToDoListCreationService()->create($user);
    }
}