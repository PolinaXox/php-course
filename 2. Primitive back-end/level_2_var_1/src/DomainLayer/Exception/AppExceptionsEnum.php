<?php

namespace App\DomainLayer\Exception;

enum AppExceptionsEnum
{
    // 400 BadRequest
    case RequestContentTypeInvalid; // 400
    case JsonInvalid;               // 400
    case RequiredFieldMissing;      // 400
    case InvalidInputDataType;      // 400
    case RequiredFieldEmpty;        // 400
    case LoginAlreadyTaken;         // 400
    case FieldValueIsNotUnique;          // 400
    case PasswordMismatch;              // 400
    case NotExistsInDatabase;           // 400
    case UnknownUser;                   // 400

    // 40x
    case SessionsConflict;              // 409 Conflict
    case SessionNotInitialized;        // 403 Forbidden


    // 500
    case FileNotExists;                  // 500
    case PersistenceException;          // 500
    case DBRecordNotFound;             // 500
    case InvalidDataType;           // 500
    case EntityAlreadyExistsInDB;    // 500


    /**
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return match ($this) {
            self::LoginAlreadyTaken,
            self::RequestContentTypeInvalid,
            self::RequiredFieldMissing,
            self::RequiredFieldEmpty,
            self::JsonInvalid,
            self::PasswordMismatch,
            self::UnknownUser,
            self::InvalidInputDataType,
            self::NotExistsInDatabase,
            self::FieldValueIsNotUnique => 400,
            self::SessionsConflict => 409,
            self::EntityAlreadyExistsInDB,
            self::PersistenceException,
            self::InvalidDataType,
            self::DBRecordNotFound,
            self::FileNotExists => 500,
            self::SessionNotInitialized => 403,
        };
    }

    /**
     * @return string
     */
    public function getHttpStatusMessage(): string
    {
        return match ($this) {
            self::LoginAlreadyTaken,
            self::RequestContentTypeInvalid,
            self::RequiredFieldMissing,
            self::RequiredFieldEmpty,
            self::JsonInvalid,
            self::PasswordMismatch,
            self::UnknownUser,
            self::InvalidInputDataType,
            self::NotExistsInDatabase,
            self::FieldValueIsNotUnique => 'Bad Request',
            self::SessionsConflict => 'Conflict',
            self::DBRecordNotFound,
            self::InvalidDataType,
            self::EntityAlreadyExistsInDB,
            self::PersistenceException,
            self::FileNotExists => 'Internal Server Error',
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
            self::InvalidInputDataType,
            self::RequestContentTypeInvalid,
            self::NotExistsInDatabase,
            self::JsonInvalid => 'Something went wrong. Please try again or contact support.',
            self::InvalidDataType,
            self::DBRecordNotFound,
            self::EntityAlreadyExistsInDB,
            self::PersistenceException,
            self::FileNotExists => 'Internal Server Error. Please try again or contact support.',
            self::FieldValueIsNotUnique => 'Field value must be unique',
            self::LoginAlreadyTaken => 'This login is already taken. Please, try another one.',
            self::PasswordMismatch,
            self::UnknownUser => 'Invalid login or password',
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
            self::RequestContentTypeInvalid => $detailsStr,
            self::NotExistsInDatabase => 'Not exists in DB.' . $detailsStr,
            self::RequiredFieldMissing => 'Request does NOT contain required field.' . $detailsStr,
            self::RequiredFieldEmpty => 'Request field is empty or contains spaces only.' . $detailsStr,
            self::FileNotExists =>  'Required file does NOT exist.' . $detailsStr,
            self::LoginAlreadyTaken,
            self::FieldValueIsNotUnique => 'Field value is not unique.' . $detailsStr,
            self::JsonInvalid => 'Input json invalid. ' . $detailsStr,
            self::PasswordMismatch => 'Authentication failed. Wrong password.',
            self::UnknownUser => 'Authentication failed. User does not exist.',
            self::SessionsConflict => 'Session conflict: tho other user is already logins in this browser',
            self::SessionNotInitialized => 'Someone try to do something. But session session not initialized.',
            self::DBRecordNotFound => 'DB Record not found.' . $detailsStr,
            self::InvalidDataType => 'Invalid data type.' . $detailsStr,
            self::InvalidInputDataType => 'Invalid input data type.' . $detailsStr,
            self::EntityAlreadyExistsInDB => 'Entity Already Exists in DB.' . $detailsStr,
            self::PersistenceException => 'Persistence Exception.' . $detailsStr,
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
            self::RequestContentTypeInvalid => $detailsStr,
            self::NotExistsInDatabase => 'Not exists in DB.' . $detailsStr,
            self::RequiredFieldMissing => 'Request does NOT contain required field.' . $detailsStr,
            self::RequiredFieldEmpty => 'Request field is empty or contains spaces only.' . $detailsStr,
            self::InvalidDataType,
            self::PersistenceException,
            self::DBRecordNotFound,
            self::FileNotExists => 'Internal Server Error.',
            self::LoginAlreadyTaken,
            self::FieldValueIsNotUnique => 'Field value is not unique.' . $detailsStr,
            self::JsonInvalid => 'Input json invalid.' . $detailsStr,
            self::PasswordMismatch => 'Authentication failed. Wrong password.',
            self::UnknownUser => 'Authentication failed. User does not exist.',
            self::SessionsConflict => 'Session conflict',
            self::SessionNotInitialized => 'Session is not initialized.',
            self::InvalidInputDataType => 'Invalid input data type.' . $detailsStr,
            self::EntityAlreadyExistsInDB => 'Entity Already Exists in DB.' . $detailsStr,
        };
    }
}

// for 401: The response MUST include a WWW-Authenticate header field...
