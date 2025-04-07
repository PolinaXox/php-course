<?php

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