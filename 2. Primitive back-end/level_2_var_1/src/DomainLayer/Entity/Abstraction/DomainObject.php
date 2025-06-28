<?php

namespace App\DomainLayer\Entity\Abstraction;

use App\DataSourceLayer\serviceDB\IdCreator as IdCreator;
use App\DomainLayer\Exception\AppException as AppException;
use App\PresentationLayer\InputValidator\AbsentValue;
use ReflectionClass as ReflectionClass;

abstract class DomainObject
{
    /**
     * @param int|AbsentValue $id
     * @throws AppException
     */
    public function __construct(protected(set) int|AbsentValue $id)
    {
        $this->id = $this->resolve($id, IdCreator::createNewId());
    }

    /**
     * @param $value
     * @param $defaultValue
     * @return mixed
     */
    protected function resolve($value, $defaultValue): string
    {
        return ($value instanceof AbsentValue) ? $defaultValue : $value;
    }

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
