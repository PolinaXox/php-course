<?php

namespace App\PresentationLayer\DataTransferObject;

use App\DomainLayer\Exception\AppException as AppException;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;
use App\PresentationLayer\InputValidator\InputValidator as InputValidator;

readonly class ToDoTaskDTO
{
    function __construct(
        public int|AbsentValue    $id,
        public string|AbsentValue $text = '',
        public bool|AbsentValue   $checked = false,
    )
    {
    }


    /**
     * @return self
     * @throws AppException
     */
    public static function forAdd(): self
    {
        $validator = new InputValidator();

        return new self(
            id: AbsentValue::instance(),
            text: $validator->getRequiredValidValue('text'),
        );
    }

    /**
     * @return self
     * @throws AppException
     */
    public static function forChange(): self
    {
        $validator = new InputValidator();

        return new self(
            id: $validator->getRequiredValidValue('id'),
            text: $validator->getRequiredValidValue('text'),
            checked: $validator->getRequiredValidValue('checked'),
        );
    }

    /**
     * @return self
     * @throws AppException
     */
    public static function forDelete(): self
    {
        return new self(
            id: new InputValidator()->getRequiredValidValue('id'),
        );
    }
}

// не подобається: індекси для $requestBody['login'] у термінах front`a
// хочу: перевести терміни front`a у терміни back`а


