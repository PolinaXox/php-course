<?php

namespace App\PresentationLayer\EndPoint;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\DomainLayer\BusinessService\ToDoTaskService as ToDoTaskService;
use App\DomainLayer\Exception\AppException as AppException;
use App\PresentationLayer\Request\RequestPreprocessor as RequestPreprocessor;
use App\PresentationLayer\Response\Response as Response;
use App\PresentationLayer\Utils\SessionConfigurator as SessionConfigurator;
use Throwable;


define('THIS_SCRIPT_METHOD', 'GET');

try {

    // middleware
    RequestPreprocessor::requireMethod(THIS_SCRIPT_METHOD);

    SessionConfigurator::configureCookies();
    session_start();
    RequestPreprocessor::requireActiveSession();

    // domain level
    $allTasks = new ToDoTaskService($_SESSION['userFile'])->getAll();

    // presentation level
    Response::success(['items' => array_values($allTasks)])->send();

} catch (AppException $ex) {
    Response::fromException($ex)->send();
} catch (Throwable $t) {
    Response::fromThrowable($t)->send();
}