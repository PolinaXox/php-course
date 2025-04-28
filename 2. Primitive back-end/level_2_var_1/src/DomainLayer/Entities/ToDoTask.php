<?php
// no usage
namespace App\entities;

use Exception;

use App\services\validationServices\UserToDoTaskDataValidatorService as Validator;

class ToDoTask extends DomainObject
{
    /**
     * @param int|null $id
     * @param string $text
     * @param bool $checked
     * @throws Exception
     */
    public function __construct(
        protected ?int $id = null,
        private string $text ='',
        private bool   $checked = false
        )
    {
        parent::__construct($id);
    }


    /**
     * @return self
     * @throws Exception
     */
    public static function createNewToDoTask(): self
    {
        $validator = new Validator();

        return new self(
            text: $validator->getValidToDoTaskText(),
        );
    }
}