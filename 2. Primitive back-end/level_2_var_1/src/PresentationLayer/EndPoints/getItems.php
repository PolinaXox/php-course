<?php
namespace App\scripts;

// PRESENTATION LAYER

require __DIR__ . '/../../vendor/autoload.php';
//require 'cookie_sets.php';

use App\DataSourceLayer\servicesDB\FileService as FileService;
use App\services\validationServices\RequestValidatorService as RequestValidatorService;
use Exception;

try {

    RequestValidatorService::validate('GET');

    // on server
    if (!isset($_COOKIE['HTTPSESSION'])) {
      throw new Exception('Session is not active', 401);
    }

    session_id($_COOKIE['HTTPSESSION']);
    session_start();

    // to front
    header('Content-Type: application/json', false);
    echo FileService::getToDoListFileContent($_SESSION['userFile']);

} catch (Exception $ex) {
    http_response_code($ex->getCode());
    echo json_encode(['exMessage' => $ex->getMessage()]);
    exit();
}
