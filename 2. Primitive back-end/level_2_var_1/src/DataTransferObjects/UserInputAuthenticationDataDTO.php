<?php

require_once(__DIR__ . '/../../vendor/autoload.php');

class UserInputAuthenticationDataDTO extends UserInputDataDTO implements RequiredAuthenticationData
{
    function __construct()
    {
        parent::__construct(self::REQUIRED_AUTHENTICATION_DATA_KEYS);
    }
}