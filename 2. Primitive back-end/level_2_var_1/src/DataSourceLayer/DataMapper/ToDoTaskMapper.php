<?php

namespace App\DataSourceLayer\DataMapper;

use App\DomainLayer\Entity\ToDoTask as ToDoTask;
use App\DomainLayer\Exception\AppException;

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

    /**
     * @param array $databaseRecord
     * @return ToDoTask
     * @throws AppException
     */
    public function mapToEntity(array $databaseRecord): ToDoTask
    {
        return new ToDoTask(
            id: $databaseRecord['id'],
            text: $databaseRecord['text'],
            checked: $databaseRecord['checked'],
        );
    }
}