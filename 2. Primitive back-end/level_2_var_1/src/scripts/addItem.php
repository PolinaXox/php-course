<?php
require __DIR__ . '/../../vendor/autoload.php';
require 'cookie_sets.php';

try {
    RequestValidatorService::validate('POST');

    // on server
    if (!isset($_COOKIE['HTTPSESSION'])) {
        throw new Exception('Session is not active', 401);
    }

    session_id($_COOKIE['HTTPSESSION']);
    session_start();
    $newTask = ToDoTask::createNewToDoTask();
    new ToDoTaskDAO()->save($newTask, $_SESSION['userFile']);

    // to front
    header('Content-Type: application/json', false);
    echo json_encode(['id' => $newTask->getId()]);

} catch (Exception $ex) {
    http_response_code($ex->getCode());
    echo json_encode(['exMessage' => $ex->getMessage()]);
    exit();
}
