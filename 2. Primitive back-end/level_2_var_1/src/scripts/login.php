<?php
//--
require __DIR__ . '/../../vendor/autoload.php';

use UserAuthenticationDataValidatorService as Authenticator;
use UserAuthorizationService as Authorizer;

try {
    RequestValidatorService::validate();
    $user = new Authenticator()->getRegisteredUser();
    new Authorizer($user);
} catch (Exception $ex) {
    http_response_code($ex->getCode());
    exit($ex->getMessage());
}
