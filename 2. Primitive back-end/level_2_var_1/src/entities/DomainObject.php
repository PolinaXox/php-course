<?php

require_once (__DIR__ . '/../../vendor/autoload.php');

abstract class DomainObject
{
    /**
     * @param int|null $id
     * @throws Exception
     */
    public function __construct(protected ?int $id = null)
    {
        $this->id = $id ?? ID_Creator::createNewId();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
