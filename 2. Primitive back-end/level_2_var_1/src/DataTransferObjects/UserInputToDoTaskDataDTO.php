<?php

require_once (__DIR__ . '/../../vendor/autoload.php');

class UserInputToDoTaskDataDTO extends UserInputDataDTO implements RequiredToDoTaskData
{
    function __construct()
    {
        parent::__construct(self::REQUIRED_TO_DO_TASK_DATA_KEYS);
    }
}