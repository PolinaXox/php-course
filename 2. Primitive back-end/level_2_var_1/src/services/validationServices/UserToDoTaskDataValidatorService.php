<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use UserInputToDoTaskDataDTO as ToDoTaskData;

class UserToDoTaskDataValidatorService
    extends InputDataValidatorService
    implements RequiredToDoTaskData
{
    private array $validToDoTaskData;

    /**
     * @throws Exception
     */
    function __construct()
    {
        parent::__construct(new ToDoTaskData()->rawInputData);
        $this->validToDoTaskData = $this->getValidToDoTaskData();
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getValidToDoTaskData(): array
    {
        $id = parent::getRawDataField(self::ID) ? $this->getValidValue(self::ID) : null;
        $text = parent::getRawDataField(self::TEXT) ? $this->getValidValue(self::TEXT) : '';
        $state = parent::getRawDataField(self::STATE) ? $this->getValidValue(self::STATE) : false;

        return [
            self::ID => $id,
            self::TEXT => $text,
            self::STATE => $state,
        ];
    }

    /**
     * @return string
     */
    public function getValidToDoTaskText(): string
    {
        return $this->validToDoTaskData[self::TEXT];
    }

    /**
     * @return int
     */
    public function getValidToDoTaskId(): int
    {
        return (int)$this->validToDoTaskData[self::ID];
    }

    /**
     * @return bool
     */
    public function getValidToDoTaskState(): bool
    {
        return (bool)$this->validToDoTaskData[self::STATE];
    }
}

