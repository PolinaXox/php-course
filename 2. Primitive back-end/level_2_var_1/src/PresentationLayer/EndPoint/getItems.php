<?php

namespace App\PresentationLayer\EndPoint;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once 'cookie_sets.php';

use App\DataSourceLayer\DAO\ToDoTaskDAO as ToDoTaskDAO;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsList as AppExceptionsList;
use Exception;

define('THIS_SCRIPT_METHOD', 'GET');

if ($_SERVER['REQUEST_METHOD'] !== THIS_SCRIPT_METHOD) {
    exit;
}

try {

    session_start();

    if (!isset($_SESSION['userFile'])) {
        throw AppException::fromEnum(AppExceptionsList::SessionNotInitialized);
    }

    // to front
    header('Content-Type: application/json', false);
    echo new ToDoTaskDAO($_SESSION['userFile'])->getAllTasksForFront();

} catch (AppException $ex) {

    // to front
    $ex->sendResponseToFront();
    exit;
} catch (Exception) {
    http_response_code(500);
    exit;
}

// I am here now!!! In the line below.