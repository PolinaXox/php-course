<?php

namespace App\DataSourceLayer\DataMapper;

use App\DomainLayer\Entity\ToDoList as ToDoList;
use App\DomainLayer\Exception\AppException as AppException;

class ToDoListMapper
{
    /**
     * @param ToDoList $toDoList
     * @return array
     */
    // ++
    public function mapToDatabaseRecord(ToDoList $toDoList): array
    {
        return $toDoList->toArray();
    }

    /**
     * @param array $databaseRecord
     * @return ToDoList
     * @throws AppException
     */
    public function mapToEntity(array $databaseRecord): ToDoList
    {
        return new ToDoList(
            userId: $databaseRecord['userId'],
            fileName: $databaseRecord['fileName'],
            id: $databaseRecord['id']
        );
    }
}