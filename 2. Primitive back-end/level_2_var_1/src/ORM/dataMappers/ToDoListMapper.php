<?php

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

    /*
    static function mapUserToJson(entities\User $user): string {
        $arr = $user->toArray();
        $key = $arr['id'];
        unset($arr['id']);
        return json_encode([$key => $arr]);
    }

    static function mapStrDBToArray(string $strDB): array {
        return self::mapToObject($strDB)->toArray();
    }
    */
}