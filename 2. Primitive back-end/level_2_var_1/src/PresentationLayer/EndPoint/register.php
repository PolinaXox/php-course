<?php
// ++
namespace App\PresentationLayer\EndPoint;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\DataSourceLayer\DAO\UserDAO as UserDAO;
use App\DomainLayer\BusinessService\UserRegistrationService as UserRegistrationService;
use App\DomainLayer\Entity\User as User;
use App\DomainLayer\Exception\AppException as AppException;
use App\PresentationLayer\DataTransferObject\UserDTO as UserDTO;
use Exception;

define('THIS_SCRIPT_METHOD', 'POST');

// on server
if ($_SERVER['REQUEST_METHOD'] !== THIS_SCRIPT_METHOD) {
    exit;
}

try {

    // on server
    $userDTO = new UserDTO();
    new UserDAO()->ensureUserLoginIsUnique($userDTO->login);
    new UserRegistrationService()->register(User::createNewUser($userDTO));

    // response to front
    header('Content-Type: application/json');
    echo json_encode(['ok' => 'true']);

} catch (AppException $ex) {

    // response to front
    $ex->sendResponseToFront();
    exit;
} catch (Exception) {
    http_response_code(500);
    exit;
}

// I am here now!!! In the line below.