<?php

namespace App\DataSourceLayer\DataMapper;

use App\DomainLayer\Entity\ToDoTask as ToDoTask;

class ToDoTaskMapper
{
    /**
     * @param ToDoTask $toDoTask
     * @return array
     */
    public function mapToDatabaseRecord(ToDoTask $toDoTask): array
    {
        return $toDoTask->toArray();
    }
}