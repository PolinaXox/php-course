<?php

namespace App\DomainLayer\BusinessService;

use App\DataSourceLayer\DAO\UserDAO as UserDAO;
use App\DomainLayer\BusinessService\ToDoListCreationService as ToDoListCreationService;
use App\DomainLayer\Entity\User as User;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsList as AppExceptionsList;
use App\PresentationLayer\DataTransferObject\UserDTO as UserDTO;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;

class UserAuthService
{
    /**
     * @param User $user
     * @return void
     * @throws AppException
     */
    public function register(User $user): void
    {
        new UserDAO()->save($user);
        new ToDoListCreationService()->create($user);
    }

    /**
     * @param UserDTO $userDTO
     * @return User
     * @throws AppException
     */
    public function getAuthenticatedUser(UserDTO $userDTO): User
    {
        $user = new UserDAO()->findByLogin($userDTO->login);                // mb AbsentValue
        $this->ensureUserIsExist($user);                                    // ex if $user = AbsentValue
        $this->ensurePasswordMatched($user->password, $userDTO->password);  // ex if mismatched

        return $user;
    }

    /**
     * @param User|AbsentValue $user
     * @return void
     * @throws AppException
     */
    private function ensureUserIsExist(User|AbsentValue $user): void
    {
        if ($user instanceof AbsentValue) {
            throw AppException::fromEnum(AppExceptionsList::UnknownUser);
        }
    }

    /**
     * @param string $passwordFromDB
     * @param string $inputPassword
     * @return void
     * @throws AppException
     */
    private function ensurePasswordMatched(string $passwordFromDB, string $inputPassword): void
    {
        if (!password_verify($inputPassword, $passwordFromDB)) {
            throw AppException::fromEnum(AppExceptionsList::PasswordMismatch);
        }
    }

}