<?php

require __DIR__ . '/../../vendor/autoload.php';

try {

    RequestValidatorService::validate();
    new UserRegistrationService()->register(User::createNewUser());

} catch (Exception $ex) {
    http_response_code($ex->getCode());
    exit($ex->getMessage());
}