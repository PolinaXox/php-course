<?php

namespace App\PresentationLayer\EndPoint;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\DataSourceLayer\DAO\ToDoTaskDAO as ToDoTaskDAO;
use App\DomainLayer\BusinessService\ToDoTaskService as ToDoTaskService;
use App\DomainLayer\Exception\AppException as AppException;
use App\PresentationLayer\DataTransferObject\ToDoTaskDTO as ToDoTaskDTO;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;
use App\PresentationLayer\InputValidator\InputValidator as InputValidator;
use App\PresentationLayer\Request\JsonDataExtractor as JsonDataExtractor;
use App\PresentationLayer\Request\RequestPreprocessor as RequestPreprocessor;
use App\PresentationLayer\Response\Response as Response;
use App\PresentationLayer\Utils\SessionConfigurator as SessionConfigurator;

use Throwable;

define('THIS_SCRIPT_METHOD', 'DELETE');

try {

    // middleware
    RequestPreprocessor::requireMethod(THIS_SCRIPT_METHOD);

    SessionConfigurator::configureCookies();
    session_start();
    RequestPreprocessor::requireActiveSession();

    // presentation layer
    $requiredData = new JsonDataExtractor()->extract('id');
    $validator = new InputValidator(
        $requiredData,
        fieldsAndRules: [
            'id' => ['checkType', 'exists'],
        ],
        checkers: [
            'id:checkType' => (fn($x) => is_int($x)),
            'id:exists' => (fn($x) => !(new ToDoTaskDAO($_SESSION['userFile'])->findByKey($x) instanceof AbsentValue)),
        ],
    );
    $taskDTO = ToDoTaskDTO::forDelete($validator->validate()->validatedData);

    // domain layer
    new ToDoTaskService($_SESSION['userFile'])->deleteTask($taskDTO);

    // presentation layer
    Response::success(['ok' => true, 'userMessage' => 'Task was deleted'])->send();

} catch (AppException $ex) {
    Response::fromException($ex)->send();
    exit;
} catch (Throwable $t) {
    Response::fromThrowable($t)->send();
    exit;
}
