<?php

// no usage

namespace App\ORM\dataMappers;

use App\entities\ToDoTask as ToDoTask;
use App\PresentationLayer\DataTransferObjects\RequiredToDoTaskData as RequiredToDoTaskData;

class ToDoTaskMapper implements RequiredToDoTaskData
{
    /**
     * @param ToDoTask $toDoTask
     * @return array
     */
    static public function mapToJsonFileItem(ToDoTask $toDoTask): array
    {
        return $toDoTask->toArray();
    }
}