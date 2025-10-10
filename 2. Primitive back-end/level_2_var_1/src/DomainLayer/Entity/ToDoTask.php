<?php

namespace App\DomainLayer\Entity;

use App\DataSourceLayer\ServiceDB\IdCreator;
use App\PresentationLayer\DataTransferObject\ToDoTaskDTO as ToDoTaskDTO;
use App\DomainLayer\Entity\Abstraction\DomainObject as DomainObject;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;
use App\DomainLayer\Exception\AppException as AppException;

class ToDoTask extends DomainObject
{
    /**
     * @param int $id
     * @param string $text
     * @param bool $checked
     */
    public function __construct(
        protected(set) int  $id,
        private(set) string $text,
        private(set) bool $checked,
    )
    {
        parent::__construct($id);
    }

    /**
     * @param ToDoTaskDTO $taskDTO
     * @return self
     * @throws AppException
     */
    public static function createNewToDoTask(ToDoTaskDTO $taskDTO): self
    {
        return new self(
            id: IdCreator::createNewId(),
            text: $taskDTO->text,
            checked: false,                 // mb not necessary, mb use default value in __construct
        );
    }
}