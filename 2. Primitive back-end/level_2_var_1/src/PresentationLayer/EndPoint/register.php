<?php

namespace App\PresentationLayer\EndPoint;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\DataSourceLayer\DAO\UserDAO as UserDAO;
use App\DomainLayer\BusinessService\UserAuthService as UserAuthService;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsEnum as AppExceptionsEnum;
use App\PresentationLayer\DataTransferObject\UserDTO as UserDTO;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;
use App\PresentationLayer\InputValidator\InputValidator as InputValidator;
use App\PresentationLayer\Request\JsonDataExtractor as JsonDataExtractor;
use App\PresentationLayer\Request\RequestPreprocessor as RequestPreprocessor;
use App\PresentationLayer\Response\Response as Response;

use Throwable;

define('THIS_SCRIPT_METHOD', 'POST');

try {

    // middleware level
    RequestPreprocessor::requireMethod(THIS_SCRIPT_METHOD);

    // presentation level
    $requiredData = new JsonDataExtractor()->extract('login', 'pass');

    $validator = new InputValidator(
        $requiredData,
        fieldsAndRules: [                                       // mb rules Enum????
            'login' => ['checkType', 'notEmpty', 'unique'],
            'pass' => ['checkType', 'notEmpty',],
        ],
        checkers: [
            'login:checkType' => (fn($x) => is_string($x)),
            'login:unique' => (fn(string $login) => new UserDAO()->findByLogin($login) instanceof AbsentValue),
            'pass:checkType' => (fn($x) => is_string($x)),
        ],
        exceptions: [
            'login:unique' => AppExceptionsEnum::LoginAlreadyTaken,
        ],
    );

    $userDTO = new UserDTO($validator->validate()->validatedData);

    // domain level
    new UserAuthService()->register($userDTO);

    // presentation level
    Response::success(['ok' => 'true', 'userMessage' => 'You have successfully registered.'])->send();

} catch (AppException $ex) {
    Response::fromException($ex)->send();
} catch (Throwable $t) {
    Response::fromThrowable($t)->send();
}