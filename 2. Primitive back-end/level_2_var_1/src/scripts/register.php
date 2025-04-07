<?php

require_once __DIR__ . '/../../vendor/autoload.php';

try {

    // on server
    RequestValidatorService::validate('POST');
    new UserRegistrationService()->register(User::createNewUser());

    // response to front
    header('Content-Type: application/json');
    echo json_encode(['ok' => 'true']);

} catch (Exception $ex) {

    // response to front
    http_response_code($ex->getCode());
    header('Content-Type: application/json');
    echo json_encode(['exMessage' => $ex->getMessage()]);
    exit();
}