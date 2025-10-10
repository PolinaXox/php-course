<?php

namespace App\PresentationLayer\EndPoint;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\DomainLayer\BusinessService\ToDoTaskService as ToDoTaskService;
use App\DomainLayer\Exception\AppException as AppException;
use App\PresentationLayer\DataTransferObject\ToDoTaskDTO as ToDoTaskDTO;
use App\PresentationLayer\InputValidator\InputValidator as InputValidator;
use App\PresentationLayer\Request\JsonDataExtractor as JsonDataExtractor;
use App\PresentationLayer\Request\RequestPreprocessor as RequestPreprocessor;
use App\PresentationLayer\Response\Response as Response;
use App\PresentationLayer\Utils\SessionConfigurator as SessionConfigurator;
use Throwable;

define('THIS_SCRIPT_METHOD', 'POST');

try {

    // middleware
    RequestPreprocessor::requireMethod(THIS_SCRIPT_METHOD);

    SessionConfigurator::configureCookies();
    session_start();
    RequestPreprocessor::requireActiveSession();

    // presentation layer
    $requiredData = new JsonDataExtractor()->extract('text');
    $validator = new InputValidator(
        $requiredData,
        fieldsAndRules: ['text' => ['checkType']],
        checkers: ['text:checkType' => (fn($x) => is_string($x))],
    );
    $taskDTO = ToDoTaskDTO::forAdd($validator->validate()->validatedData);

    // domain layer
    $newTask = new ToDoTaskService($_SESSION['userFile'])->addTask($taskDTO);

    // presentation layer
    Response::success(['id' => $newTask->id, 'userMessage' => 'Task was added'])->send();

} catch (AppException $ex) {
    Response::fromException($ex)->send();
    exit;
} catch (Throwable $t) {
    Response::fromThrowable($t)->send();
    exit;
}

// ToDo set_error_handler()
// ToDo set_exception_handler()
