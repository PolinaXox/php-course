<?php
namespace App\scripts;

// PRESENTATION LAYER

require __DIR__ . '/../../vendor/autoload.php';
//require 'cookie_sets.php';

use Exception;

use App\services\validationServices\RequestValidatorService;
use App\services\businessServices\UserRegistrationService as UserRegistrationService;
use App\entities\User as User;

try {

    // on server
    RequestValidatorService::validate('POST');

    new UserRegistrationService()->register(User::createNewUser());

    // response to front
    header('Content-Type: application/json');
    echo json_encode(['ok' => 'true', 'aaa' => '***']);


} catch (Exception $ex) {
    // response to front
    http_response_code($ex->getCode());
    header('Content-Type: application/json');
    echo json_encode(['exMessage' => $ex->getMessage()]);
    exit();
}