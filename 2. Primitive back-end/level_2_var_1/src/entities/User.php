<?php

require_once (__DIR__ . '/../../vendor/autoload.php');

use UserRegistrationDataValidatorService as Validator;

class User extends DomainObject
{
    /**
     * @param string $login
     * @param string $password
     * @param int|null $id
     * @throws Exception
     */
    public function __construct(
        private readonly string $login,
        private readonly string $password,
        ?int                    $id = null
    )
    {
        parent::__construct($id);
    }

    /**
     * Used for registration
     *
     * @return self
     * @throws Exception
     */
    public static function createNewUser(): self
    {
        $data = Validator::getValidRegistrationData() ;
        return new self(
            login: $data[RequiredRegistrationData::LOGIN],
            password: $data[RequiredRegistrationData::PASSWORD]
        );
    }

    // METHODS. GETTERS
    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}