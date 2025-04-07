<?php

class ToDoTaskMapper implements RequiredToDoTaskData
{

//      no usages
//    /**
//     * @param array|null $taskAsArray
//     * @return ToDoTask|null
//     * @throws Exception
//     */
//    static public function mapToObject(?array $taskAsArray): ?ToDoTask
//    {
//        if (!$taskAsArray)
//            return null;
//        return new ToDoTask(
//            $taskAsArray[self::ID],
//            $taskAsArray[self::TEXT],
//            $taskAsArray[self::STATE],
//        );
//    }

    /**
     * @param ToDoTask $toDoTask
     * @return array
     */
    static public function mapToJsonFileItem(ToDoTask $toDoTask): array
    {
        return $toDoTask->toArray();
    }
}