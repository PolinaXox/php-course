<?php
require __DIR__ . '/../../vendor/autoload.php';
require 'cookie_sets.php';

try {

    // on server
    if (!isset($_COOKIE['HTTPSESSION'])) {
        throw new Exception('Session is not active', 401);
    }

    session_id($_COOKIE['HTTPSESSION']);
    session_start();

    // to front
    header('Content-Type: application/json', false);
    header("Set-Cookie: HTTPSESSION=; Path=/; Secure; HttpOnly; SameSite=None; Partitioned; Expires=Thu, 01 Jan 1970 00:00:00 GMT", false);
    echo json_encode(['ok' => true]);

    // on server
    session_destroy();
    session_abort();

} catch (Exception $ex) {
    http_response_code($ex->getCode());
    echo json_encode(['exMessage' => $ex->getMessage()]);
    exit();
}