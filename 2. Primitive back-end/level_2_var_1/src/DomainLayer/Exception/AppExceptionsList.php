<?php

namespace App\DomainLayer\Exception;

enum AppExceptionsList : int
{
    case RequiredFieldEmpty = 1;            // 400
    case FileNotExist = 2;                  // 500
    case JsonInvalid = 3;                   // 400
    case LoginIsNotUnique = 4;              // 400
    case PasswordMismatch = 5;              // 400
    case RequiredFieldMissing = 6;          // 400
    case UnknownUser = 7;                   // 400
    case SessionsConflict = 9;              // 409 Conflict
    case SessionNotInitialized = 10;        // 403 Forbidden
    case DBRecordNotFound = 11;             // 500

    /**
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return match ($this) {
            self::RequiredFieldMissing,
            self::RequiredFieldEmpty,
            self::JsonInvalid,
            self::PasswordMismatch,
            self::UnknownUser,
            self::LoginIsNotUnique => 400,
            self::SessionsConflict => 409,
            self:: DBRecordNotFound,
            self::FileNotExist => 500,
            self::SessionNotInitialized => 403,
        };
    }

    /**
     * @return string
     */
    public function getHttpStatusMessage(): string
    {
        return match ($this) {
            self::RequiredFieldMissing,
            self::RequiredFieldEmpty,
            self::JsonInvalid,
            self::PasswordMismatch,
            self::UnknownUser,
            self::LoginIsNotUnique => 'Bad Request',
            self::SessionsConflict => 'Conflict',
            self::DBRecordNotFound,
            self::FileNotExist => 'Internal Server Error',
            self::SessionNotInitialized => 'Forbidden',
        };
    }

    /**
     * @return string
     */
    public function getUserMessage(): string
    {
        return match ($this) {
            self::RequiredFieldEmpty => 'Please fill all the required fields.',
            self::RequiredFieldMissing,
            self::JsonInvalid => 'Something went wrong. Please try again or contact support.',
            self::DBRecordNotFound,
            self::FileNotExist => 'Internal Server Error. Please try again or contact support.',
            self::LoginIsNotUnique => 'This login is already taken. Please, try another one.',
            self::PasswordMismatch, self::UnknownUser => 'Invalid login or password',
            self::SessionsConflict => 'There is an active session on the browser yet. Please finish the session and try again.',
            self::SessionNotInitialized => 'You are not authorized now. Please login.',
        };
    }

    /**
     * @param array $details
     * @return string
     */
    public function getBackDevMessage(array $details): string
    {
        $detailsStr = $details ? " Details: " . implode(', ', $details) : '';

        return match ($this) {
            self::RequiredFieldMissing => 'Request does NOT contain required field.' . $detailsStr,
            self::RequiredFieldEmpty => 'Request field is empty or contains spaces only.' . $detailsStr,
            self::FileNotExist =>  'Required file does NOT exist.' . $detailsStr,
            self::LoginIsNotUnique => 'Registration failed. User login is not unique.',
            self::JsonInvalid => 'Input json invalid. Details: ' . $detailsStr,
            self::PasswordMismatch => 'Authentication failed. Wrong password.',
            self::UnknownUser => 'Authentication failed. User does not exist.',
            self::SessionsConflict => 'Session conflict: tho other user is already logins in this browser',
            self::SessionNotInitialized => 'Someone try to do something. But session session not initialized.',
            self::DBRecordNotFound => 'DB Record not found.' . $detailsStr,
        };
    }

    /**
     * @param array $details
     * @return string
     */
    public function getFrontDevMessage(array $details): string
    {
        $detailsStr = $details ? " Details: " . implode(', ', $details) : '';

        return match ($this) {
            self::RequiredFieldMissing => 'Request does NOT contain required field.' . $detailsStr,
            self::RequiredFieldEmpty => 'Request field is empty or contains spaces only.' . $detailsStr,
            self::DBRecordNotFound,
            self::FileNotExist => 'Internal Server Error.',
            self::LoginIsNotUnique => 'Registration failed. User login is not unique.',
            self::JsonInvalid => 'Input json invalid. Details: ' . $detailsStr,
            self::PasswordMismatch => 'Authentication failed. Wrong password.',
            self::UnknownUser => 'Authentication failed. User does not exist.',
            self::SessionsConflict => 'Session conflict',
            self::SessionNotInitialized => 'Session is not initialized.',
        };
    }
}

// for 401: The response MUST include a WWW-Authenticate header field...
