<?php
// enum??????????
interface RequiredRegistrationData
{
    const string LOGIN = 'login';
    const string PASSWORD = 'pass';
    const array REQUIRED_REGISTRATION_DATA_KEYS = [self::LOGIN, self::PASSWORD];
}
