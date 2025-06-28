<?php

namespace App\PresentationLayer\EndPoint;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsList as AppExceptionsList;
use Exception as Exception;

define('THIS_SCRIPT_METHOD', 'POST');

if ($_SERVER['REQUEST_METHOD'] !== THIS_SCRIPT_METHOD) {
    exit;
}

try {

    session_start();

    if (!isset($_SESSION['userFile'])) {
        throw AppException::fromEnum(AppExceptionsList::SessionNotInitialized);
    }

    session_unset();
    session_destroy();

    // to front
    header('Content-Type: application/json', false);
    echo json_encode(['ok' => true]);

} catch (AppException $ex) {
    $ex->sendResponseToFront();
    exit;
} catch (Exception) {
    http_response_code(500);
    exit;
}

//  header('Set-Cookie: sessionId=; Max-Age=0; Secure; HttpOnly; SameSite=None; Path=/; Partitioned;', false);
