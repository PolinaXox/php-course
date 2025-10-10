<?php

namespace App\DomainLayer\BusinessService;

use App\DataSourceLayer\DAO\UserDAO as UserDAO;
use App\DomainLayer\BusinessService\ToDoListService as ToDoListService;
use App\DomainLayer\Entity\User as User;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsEnum as AppExceptionsEnum;
use App\PresentationLayer\DataTransferObject\UserDTO as UserDTO;

class UserAuthService
{
    /**
     * @param UserDTO $dto
     * @return User
     * @throws AppException
     */
    public function register(UserDTO $dto): User
    {
        // збереження нового користувача в БД
        $user = User::createNewUser($dto);
        new UserDAO()->create($user);

        // створення ЗАПИСУ(!!!) о ТуДуЛисті користувача
        new ToDoListService()->createRecord($user);

        return $user;
    }

    /**
     * @param UserDTO $userDTO
     * @return User
     * @throws AppException
     */
    public function authenticate(UserDTO $userDTO): User
    {
        $user = new UserDAO()->findByLogin($userDTO->login);
        $this->ensurePasswordMatched($user->password, $userDTO->password);  // ex if mismatched

        return $user;
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
            throw AppException::fromEnum(AppExceptionsEnum::PasswordMismatch);
        }
    }

}