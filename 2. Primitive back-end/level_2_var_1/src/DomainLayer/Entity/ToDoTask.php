<?php

namespace App\DomainLayer\Entity;

use App\PresentationLayer\DataTransferObject\ToDoTaskDTO as ToDoTaskDTO;
use App\DomainLayer\Entity\Abstraction\DomainObject as DomainObject;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;
use App\DomainLayer\Exception\AppException as AppException;

class ToDoTask extends DomainObject
{
    /**
     * @param int|AbsentValue $id
     * @param string|AbsentValue $text
     * @param bool|AbsentValue $checked
     * @throws AppException
     */
    public function __construct(
        protected(set) int|AbsentValue  $id,
        private(set) string|AbsentValue $text,
        private(set) bool|AbsentValue   $checked,
    )
    {
        parent::__construct($id);
        //$this->text = $this->resolve($this->text, 'Some task');     // unnecessary action ?????? now constr uses only for new tasks
        //$this->checked = $this->resolve($checked, false);           // unnecessary action ??????
    }


    /**
     * @param ToDoTaskDTO $taskDTO
     * @return self
     * @throws AppException
     */
    public static function createNewToDoTask(ToDoTaskDTO $taskDTO): self
    {
        return new self(
            id: AbsentValue::instance(),        // now $taskDTO->id == always AbsentValue::instance()
            text: $taskDTO->text,
            checked: false,                     // now $taskDTO->checked == always false
        );
    }
}