<?php

namespace App\DomainLayer\Entity\Abstraction;

use ReflectionClass as ReflectionClass;

abstract class DomainObject
{
    /**
     * @param int $id
     */
    public function __construct(
        protected(set) int $id
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        $properties = new ReflectionClass($this)->getProperties();

        foreach ($properties as $property) {
            $array[$property->getName()] = $property->getValue($this);
        }

        return $array;
    }
}

// перевірити, як працює кастинг array з полями public private(set)
