<?php

namespace App\DomainLayer\Entity;

use App\DomainLayer\Entity\Abstraction\DomainObject as DomainObject;
use App\PresentationLayer\DataTransferObject\UserDTO as UserDTO;
use App\DomainLayer\Exception\AppException as AppException;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;

class User extends DomainObject
{
    // as an experiment... instead of a private field and its public getter
    private(set) string $login;
    private(set) string $password;

    /**
     * @param string $login
     * @param string $password
     * @param int|AbsentValue $id
     * @throws AppException
     */
    public function __construct(
        string $login,
        string $password,
        int|AbsentValue $id,
    )
    {
        parent::__construct($id);
        $this->password = $password;
        $this->login = $login;
    }

    /**
     * @param UserDTO $dto
     * @return self
     * @throws AppException
     */
    public static function createNewUser(UserDTO $dto): self
    {
        return new self(
            login: $dto->login,
            password: password_hash($dto->password, PASSWORD_DEFAULT),
            id: AbsentValue::instance(),
        );
    }
}