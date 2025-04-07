<?php
require __DIR__ . '/../../vendor/autoload.php';
require 'cookie_sets.php';

try {
    RequestValidatorService::validate('DELETE');

    // on server
    if (!isset($_COOKIE['HTTPSESSION'])) {
        throw new Exception('Session is not active', 401);
    }

    session_id($_COOKIE['HTTPSESSION']);
    session_start();

    $index = new UserToDoTaskDataValidatorService()->getValidToDoTaskId();
    //echo $index;
    new ToDoTaskDAO()->delete($index, $_SESSION['userFile']);

    // to front
    header('Content-Type: application/json', false);
    echo json_encode(['ok' => true]);

} catch (Exception $ex) {
    http_response_code($ex->getCode());
    echo json_encode(['exMessage' => $ex->getMessage()]);
    exit();
}
