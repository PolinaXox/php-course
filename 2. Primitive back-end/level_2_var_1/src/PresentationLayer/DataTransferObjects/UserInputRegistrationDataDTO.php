<?php
// no usage
namespace App\PresentationLayer\DataTransferObjects;

class UserInputRegistrationDataDTO extends UserInputDataDTO implements RequiredRegistrationData
{
    function __construct()
    {
        parent::__construct(self::REQUIRED_REGISTRATION_DATA_KEYS);
    }
}