<?php

namespace App\DomainLayer\Entity;

use App\DomainLayer\Entity\Abstraction\DomainObject as DomainObject;

class ToDoList extends DomainObject
{
    /**
     * @param int $id
     * @param int $userId
     * @param string $fileName
     */
    public function __construct(
        protected(set) int $id,
        private(set) int $userId,
        private(set) string $fileName,
    )
    {
        parent::__construct($id);
    }
}