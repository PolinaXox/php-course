<?php

namespace App\PresentationLayer\EndPoint;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once 'cookie_sets.php';

use App\DataSourceLayer\DAO\ToDoTaskDAO as ToDoTaskDAO;
use App\DomainLayer\Entity\ToDoTask as ToDoTask;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsList as AppExceptionsList;
use App\PresentationLayer\DataTransferObject\ToDoTaskDTO as ToDoTaskDTO;
use Exception;

define('THIS_SCRIPT_METHOD', 'POST');

if ($_SERVER['REQUEST_METHOD'] !== THIS_SCRIPT_METHOD) {
    exit;
}

try {

    session_start();

    if (!isset($_SESSION['userFile'])) {
        throw AppException::fromEnum(AppExceptionsList::SessionNotInitialized);
    }

    $taskDTO = ToDoTaskDTO::forAdd();
    $newTask = ToDoTask::createNewToDoTask($taskDTO);
    new ToDoTaskDAO($_SESSION['userFile'])->save($newTask);

    // to front
    header('Content-Type: application/json', false);
    echo json_encode(['id' => $newTask->id]);

} catch (AppException $ex) {

    // to front
    $ex->sendResponseToFront();
    exit;
}  catch (Exception) {
    http_response_code(500);
    exit;
}

// To Do set_error_handler()
// To Do set_exception_handler()
