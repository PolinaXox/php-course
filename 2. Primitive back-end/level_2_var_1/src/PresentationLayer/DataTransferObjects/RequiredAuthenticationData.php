<?php
//+
namespace App\PresentationLayer\DataTransferObjects;

interface RequiredAuthenticationData
{
    const string LOGIN = 'login';
    const string PASSWORD = 'pass';
    const array REQUIRED_AUTHENTICATION_DATA_KEYS = [self::LOGIN, self::PASSWORD];
}
