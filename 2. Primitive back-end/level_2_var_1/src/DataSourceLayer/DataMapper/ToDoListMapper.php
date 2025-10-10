<?php

namespace App\DataSourceLayer\DataMapper;

use App\DomainLayer\Entity\ToDoList as ToDoList;

class ToDoListMapper
{
    /**
     * @param ToDoList $toDoList
     * @return array
     */
    public function mapToDatabaseRecord(ToDoList $toDoList): array
    {
        return $toDoList->toArray();
    }

    /**
     * @param array $databaseRecord
     * @return ToDoList
     */
    public function mapToEntity(array $databaseRecord): ToDoList
    {
        return new ToDoList(
            id: $databaseRecord['id'],
            userId: $databaseRecord['userId'],
            fileName: $databaseRecord['fileName']
        );
    }
}