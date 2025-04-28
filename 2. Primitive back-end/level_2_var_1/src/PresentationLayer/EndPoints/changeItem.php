<?php
namespace App\scripts;

// PRESENTATION LAYER

require __DIR__ . '/../../vendor/autoload.php';
//require 'cookie_sets.php';

use Exception;

use App\services\validationServices\RequestValidatorService;
use App\ORM\DAO\ToDoTaskDAO as ToDoTaskDAO;

try {
    RequestValidatorService::validate('PUT');

    // on server
    if (!isset($_COOKIE['HTTPSESSION'])) {
        throw new Exception('Session is not active', 401);
    }

    session_id($_COOKIE['HTTPSESSION']);
    session_start();

    new ToDoTaskDAO()->update($_SESSION['userFile']);

    // to front
    header('Content-Type: application/json', false);
    echo json_encode(['ok' => true]);

} catch (Exception $ex) {
    http_response_code($ex->getCode());
    echo json_encode(['exMessage' => $ex->getMessage()]);
    exit();
}
