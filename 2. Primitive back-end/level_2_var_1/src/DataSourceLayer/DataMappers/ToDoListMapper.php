<?php

namespace App\ORM\dataMappers;

use App\entities\ToDoList as ToDoList;

class ToDoListMapper
{
    /**
     * @param string|null $strDB
     * @return ToDoList|null
     */
    static public function mapToObject(?string $strDB): ?ToDoList
    {
        return $strDB ? unserialize($strDB) : null;
    }

    /**
     * @param ToDoList $toDoList
     * @return string
     */
    static public function mapToStringDB(ToDoList $toDoList): string
    {
        return serialize($toDoList);
    }
}