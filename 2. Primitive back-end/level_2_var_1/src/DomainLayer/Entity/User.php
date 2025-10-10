<?php

namespace App\DomainLayer\Entity;

use App\DataSourceLayer\ServiceDB\IdCreator as IdCreator;
use App\DomainLayer\Entity\Abstraction\DomainObject as DomainObject;
use App\DomainLayer\Exception\AppException as AppException;
use App\PresentationLayer\DataTransferObject\UserDTO as UserDTO;


class User extends DomainObject
{
    /**
     * @param int $id
     * @param string $login
     * @param string $password
     */
    public function __construct(
        protected(set) int $id,
        private(set) string $login,
        private(set) string $password

    )
    {
        parent::__construct($id);
    }

    /**
     * @param UserDTO $dto
     * @return self
     * @throws AppException
     */
    public static function createNewUser(UserDTO $dto): self
    {
        return new self(
            id: IdCreator::createNewId(),
            login: $dto->login,
            password: password_hash($dto->password, PASSWORD_DEFAULT),
        );
    }
}