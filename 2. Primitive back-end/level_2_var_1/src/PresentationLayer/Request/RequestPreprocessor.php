<?php

namespace App\PresentationLayer\Request;

use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsEnum as AppExceptionsEnum;
use App\PresentationLayer\Response\Response;

class RequestPreprocessor
{
    /**
     * @param string $requiredMethod
     * @return void
     */
    public static function requireMethod(string $requiredMethod): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== $requiredMethod) { // mb 2 перевірки? (OPT, requiredMethod, MethodNotAllowed?)
            Response::corsPreflight()->sendNoContent();
            exit;
        }
    }

    /**
     * @return void
     * @throws AppException
     */
    public static function requireNoActiveSession(): void
    {
        if (isset($_SESSION['userFile'])) {
            throw AppException::fromEnum(AppExceptionsEnum::SessionsConflict);
        }
    }

    /**
     * @return void
     * @throws AppException
     */
    public static function requireActiveSession(): void
    {
        if (!isset($_SESSION['userFile'])) {
            throw AppException::fromEnum(AppExceptionsEnum::SessionNotInitialized);
        }
    }
}
