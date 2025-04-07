<?php
require __DIR__ . '/../../vendor/autoload.php';
require 'cookie_sets.php';

use UserAuthenticationService as Authenticator;
use UserAuthorizationService as Authorizer;

session_start();

try {

    // on server
    RequestValidatorService::validate('POST');
    $registerUser = Authenticator::getRegisteredUser();
    Authorizer::authorize($registerUser);

    // response to front
    header('Set-Cookie: HTTPSESSION=' . session_id() . '; Secure; HttpOnly; SameSite=None; Path=/; Partitioned;', false);
   // header('Set-Cookie: userFile=' . $_SESSION['userFile'] . '; Secure; HttpOnly; SameSite=None; Path=/; Partitioned;', false);

    header('Content-Type: application/json', false);
    echo json_encode(['ok' => 'true']);

    // on server (?????????)
    session_regenerate_id(false);

} catch (Exception $ex) {

    // response to front
    http_response_code($ex->getCode());
    echo json_encode(['exMessage' => $ex->getMessage()]);
    exit();
}