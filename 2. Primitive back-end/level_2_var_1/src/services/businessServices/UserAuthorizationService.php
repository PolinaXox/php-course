<?php

require_once (__DIR__ . '/../../../vendor/autoload.php');
// --
class UserAuthorizationService
{
    function __construct(
        private User $user
    )
    {
        var_dump($this->user);
    }


//    public function getUserToDoList($user): ToDoList
//    {
//        return new ToDoListDAO()->getToDoList($user);
//    }

//    function getUserFile(User $user): string
//    {
//        return ToDoListDAO::getUserToDoListFilePath($user);
//    }

}