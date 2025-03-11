<?php

require_once (__DIR__ . '/../../vendor/autoload.php');

class UserInputRegistrationDataDTO extends UserInputDataDTO implements RequiredRegistrationData
{
    function __construct()
    {
        parent::__construct(self::REQUIRED_REGISTRATION_DATA_KEYS);
    }
}