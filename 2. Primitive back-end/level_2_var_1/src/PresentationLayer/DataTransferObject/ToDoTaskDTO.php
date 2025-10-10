<?php

namespace App\PresentationLayer\DataTransferObject;

use App\PresentationLayer\InputValidator\AbsentValue;

readonly class ToDoTaskDTO
{
    /**
     * @param int|AbsentValue $id
     * @param string|AbsentValue $text
     * @param bool|AbsentValue $checked
     */
    function __construct(
        public int|AbsentValue    $id,
        public string|AbsentValue $text,
        public bool|AbsentValue   $checked,
    )
    {
    }

    /**
     * @param array $validatedData
     * @return self
     */
    public static function forAdd(array $validatedData): self
    {
        return new self(
            id: AbsentValue::instance(),
            text: $validatedData['text'],
            checked: AbsentValue::instance(),
        );
    }

    /**
     * @param array $validatedData
     * @return self
     */
    public static function forChange(array $validatedData): self
    {
        return new self(
            id: $validatedData['id'],
            text: $validatedData['text'],
            checked: $validatedData['checked'],
        );
    }

    /**
     * @param array $validatedData
     * @return self
     */
    public static function forDelete(array $validatedData): self
    {
        return new self(
            id: $validatedData['id'],
            text: AbsentValue::instance(),
            checked: AbsentValue::instance(),
        );
    }
}


