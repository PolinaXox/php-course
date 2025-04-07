<?php
require_once(__DIR__ . '/../../../vendor/autoload.php');

class ToDoListDAO
{
    const string TO_DO_LISTS_FILE = __DIR__ . '/../../../FileDB/to_do_lists.txt';

    /**
     * @param User $user
     * @return string
     * @throws Exception
     */
    static function getUserToDoListFileName(User $user): string
    {
        return new ToDoListDAO()->getToDoList($user)->getFileName();
    }

    /**
     * @param User $user
     * @return toDoList
     * @throws Exception
     */
    function getToDoList(User $user): toDoList
    {
        $toDoList = self::findByUser($user);

        return $toDoList ?? ToDoList::createNewToDoList($user);
    }

    /**
     * @param User $user
     * @return ToDoList|null
     * @throws Exception
     */
    public function findByUser(User $user): ?ToDoList
    {
        return $this->findFirstByOneCriteria('userID', $user->getId());
    }

    /**
     * @param string $criteria
     * @param string $value
     * @return ToDoList|null
     * @throws Exception
     */
    private function findFirstByOneCriteria(string $criteria, string $value): ?ToDoList
    {
        FileService::ensureFileExists(self::TO_DO_LISTS_FILE);
        $file = fopen(self::TO_DO_LISTS_FILE, 'r');
        $toDoList = $this->findFirstInFile($file, $criteria, $value);
        fclose($file);
        return $toDoList ?? null;
    }

    /**
     * @param $file
     * @param string $criteria
     * @param string $value
     * @return ToDoList|null
     */
    private function findFirstInFile($file, string $criteria, string $value): ?ToDoList
    {
        while (!feof($file)) {
            if ($toDoList = self::findInLine(fgets($file), $criteria, $value)) {
                return $toDoList;
            }
        }

        return null;
    }

    /**
     * @param string $line
     * @param string $criteria
     * @param string $value
     * @return ToDoList|null
     */
    private function findInLine(string $line, string $criteria, string $value): ?ToDoList
    {
        $toDoList = ToDoListMapper::mapToObject(trim($line)) ?? null;

        if (!$toDoList || ($toDoList->toArray()[$criteria] ?? null) !== $value) {
            return null;
        }

        return $toDoList;
    }

    /**
     * @param ToDoList $toDoList
     * @return bool
     * @throws Exception
     */
    public function save(ToDoList $toDoList): bool
    {
        return file_put_contents(
            self::TO_DO_LISTS_FILE,
            ToDoListMapper::mapToStringDB($toDoList) . PHP_EOL,
            FILE_APPEND
        );
    }
}