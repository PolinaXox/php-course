<?php
//--
require_once (__DIR__ . '/../../../vendor/autoload.php');

class ToDoListDAO
{
    const string TO_DO_LISTS_FILE = __DIR__ . '/../../../FileDB/to_do_lists.txt';
    const string TO_DO_LISTS_DIR = __DIR__ . '/../../../FileDB/ToDoLists/';

    /**
     * @param User $user
     * @return toDoList
     * @throws Exception
     */
    //--
    function getToDoList(User $user): toDoList
    {
        $toDoList = self::findByUser($user->getId());

        return $toDoList ?? ToDoList::createNewToDoList($user);
    }

    /**
     * @param User $user
     * @return string
     * @throws Exception
     */
    static function getUserToDoListFilePath(User $user): string
    {
        $fileName = new ToDoListDAO()->getToDoList($user)->getFileName();
        return self::TO_DO_LISTS_DIR . $fileName;
    }

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
     * @param string $userId
     * @return ToDoList|null
     * @throws Exception
     */
    public function findByUser(string $userId): ?ToDoList
    {
        return $this->findFirstByOneCriteria('userId', $userId);
    }

    /**
     * @param ToDoList $toDoList
     * @return bool
     * @throws Exception
     */
    public function save(ToDoList $toDoList): bool
    {
        // right place to create file????????
        FileService::createToDoListFilePhysically(self::TO_DO_LISTS_DIR . $toDoList->getFileName());

        return file_put_contents(
            self::TO_DO_LISTS_FILE,
            ToDoListMapper::mapToStringDB($toDoList) . PHP_EOL,
            FILE_APPEND
        );
    }

    /**
     * @param string $fileName
     * @return bool
     */
    private function createToDoListFilePhysically(string $fileName) : bool
    {
        return file_put_contents(self::TO_DO_LISTS_DIR . $fileName, json_encode(['items' => []]));
    }


//    /**
//     * @param int $id
//     * @return entities\User|null
//     * @throws Exception
//     */
//    # from DAO\UserDAO
//    public function find(int $id): ?entities\User
//    {
//        return $this->findFirstByOneCriteria('id', $id);
//    }
//
//    /**
//     * @param string $login
//     * @return entities\User|null
//     * @throws Exception
//     */
//    # from DAO\UserDAO
//    public function findByLogin(string $login): ?entities\User
//    {
//        return $this->findFirstByOneCriteria('login', $login);
//    }
//

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
//
//
//
//    function delete(int $id): bool
//    {
//        return false;
//    }
//
//    function update(int $id, entities\User $user): bool
//    {
//        return false;
//    }

}