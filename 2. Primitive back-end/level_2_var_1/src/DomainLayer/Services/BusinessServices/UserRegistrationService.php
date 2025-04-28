<?php

namespace App\services\businessServices;

use Exception;

use App\entities\User;
use App\ORM\DAO\UserDAO;
use App\ORM\DAO\ToDoListDAO;
use App\entities\ToDoList;

class UserRegistrationService
{
    /**
     * @param User $user
     * @return void
     * @throws Exception
     */
    public function register(User $user): void
    {
        new UserDAO()->save($user);
        new ToDoListDAO()->save(ToDoList::createNewToDoList($user));
    }
}