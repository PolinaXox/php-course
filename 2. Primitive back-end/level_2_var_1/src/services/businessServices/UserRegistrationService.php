<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

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