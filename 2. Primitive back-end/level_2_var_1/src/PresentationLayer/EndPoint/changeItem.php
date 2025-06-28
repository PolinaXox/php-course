<?php

namespace App\PresentationLayer\EndPoint;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once 'cookie_sets.php';

use App\DataSourceLayer\DAO\ToDoTaskDAO as ToDoTaskDAO;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsList as AppExceptionsList;
use App\PresentationLayer\DataTransferObject\ToDoTaskDTO as ToDoTaskDTO;
use Exception as Exception;

define('THIS_SCRIPT_METHOD', 'PUT');

if ($_SERVER['REQUEST_METHOD'] !== THIS_SCRIPT_METHOD) {
    exit;
}

try {

    session_start();

    if (!isset($_SESSION['userFile'])) {
        throw AppException::fromEnum(AppExceptionsList::SessionNotInitialized);
    }

    $taskDTO = ToDoTaskDTO::forChange();
    new ToDoTaskDAO($_SESSION['userFile'])->update($taskDTO);

    // to front
    header('Content-Type: application/json', false);
    echo json_encode(['ok' => true]);

} catch (AppException $ex) {
    $ex->sendResponseToFront();
   exit;
} catch (Exception) {
    http_response_code(500);
    exit;
}

