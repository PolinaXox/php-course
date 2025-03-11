<?php

require_once (__DIR__ . '/../../vendor/autoload.php');

class ToDoList extends DomainObject
{
    private const string FILE_NAME_PREFIX = 'toDoList_';
    private const string FILE_EXTENSION = '.json';
    const string TO_DO_LISTS_DIR = __DIR__ . '/../../../FileDB/toDoLists/';

    /**
     * @param string $userID
     * @param string $fileName
     * @param int|null $id
     *
     * @throws Exception
     */
    public function __construct(
        private readonly string $userID,
        private readonly string $fileName,
        ?int                    $id = null)
    {
        parent::__construct($id);
    }


    /**
     * New toDoList creates during user`s registration
     *
     * @param User $user
     * @return self
     * @throws Exception
     */
    public static function createNewToDoList(User $user): self
    {
        $fileName = self::FILE_NAME_PREFIX . $user->getLogin() . self::FILE_EXTENSION;
        return new self($user->getId(), $fileName);
    }

    /**
     * @return string
     */
    public function getUserID(): string
    {
        return $this->userID;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}