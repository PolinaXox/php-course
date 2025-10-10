<?php

namespace App\PresentationLayer\EndPoint;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\DomainLayer\Exception\AppException as AppException;
use App\PresentationLayer\Request\RequestPreprocessor as RequestPreprocessor;
use App\PresentationLayer\Response\Response as Response;
use Throwable as Throwable;

define('THIS_SCRIPT_METHOD', 'POST');

try {

    // middleware level
    RequestPreprocessor::requireMethod(THIS_SCRIPT_METHOD);
    session_start();
    RequestPreprocessor::requireActiveSession();

    // server
    session_unset();
    session_destroy();

    // presentation level
    Response::success(['ok' => 'true', 'userMessage' => 'Bye! See you soon!'])->send();

} catch (AppException $ex) {
    Response::fromException($ex)->send();
} catch (Throwable $t) {
    Response::fromThrowable($t)->send();
}