<?php
// no usage

namespace App\PresentationLayer\DataTransferObjects;

class UserInputAuthenticationDataDTO extends UserInputDataDTO implements RequiredAuthenticationData
{
    function __construct()
    {
        parent::__construct(self::REQUIRED_AUTHENTICATION_DATA_KEYS);
    }
}