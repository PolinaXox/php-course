<?php

namespace App\DomainLayer\Entity;

use App\DomainLayer\Entity\Abstraction\DomainObject as DomainObject;
use App\DomainLayer\Exception\AppException as AppException;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;

class ToDoList extends DomainObject
{
    /**
     * @param int $userId
     * @param string $fileName
     * @param int|AbsentValue $id
     * @throws AppException
     */
    public function __construct(
        private(set) int    $userId,
        private(set) string $fileName,
        protected(set) int|AbsentValue  $id)
    {
        parent::__construct($id);
    }

    /**
     * @param int $userId
     * @param string $fileName
     * @return self
     * @throws AppException
     */
    public static function createNewToDoList(int $userId, string $fileName) : self
    {
        return new self(
            userId: $userId,
            fileName: $fileName,
            id: AbsentValue::instance()
        );
    }
}