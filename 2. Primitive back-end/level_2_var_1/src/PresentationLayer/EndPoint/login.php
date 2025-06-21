<?php
// ++
namespace App\PresentationLayer\EndPoint;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once 'cookie_sets.php';

use App\DataSourceLayer\DAO\ToDoListDAO as ToDoListDAO;
use App\DomainLayer\BusinessService\UserAuthenticationService as Authenticator;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsList as AppExceptionsList;
use App\PresentationLayer\DataTransferObject\UserDTO as UserDTO;
use Exception;

define('THIS_SCRIPT_METHOD', 'POST');

if ($_SERVER['REQUEST_METHOD'] !== THIS_SCRIPT_METHOD) {
    exit;
}

try {

    session_start();

    if (isset($_SESSION['userFile'])) {
        throw AppException::fromEnum(AppExceptionsList::SessionsConflict);
    }

    // on server
    $userDTO = new UserDTO();
    $user = new Authenticator()->getAuthenticatedUser($userDTO);
    $toDoList = new ToDoListDAO()->getUserToDoList($user->id);

    session_regenerate_id(true);
    $_SESSION['userFile'] = $toDoList->fileName; // getFileName();

    // to front
    header('Content-Type: application/json', false);
    echo json_encode(['ok' => 'true']);

} catch (AppException $ex) {

    // response to front
    $ex->sendResponseToFront();
    exit;
} catch (Exception) {
    http_response_code(500);
    exit;
}

//header('Set-Cookie: sessionId=' . session_id() . '; Secure; HttpOnly; SameSite=None; Path=/; Partitioned;', false);

// I am here now!!! In the line below.