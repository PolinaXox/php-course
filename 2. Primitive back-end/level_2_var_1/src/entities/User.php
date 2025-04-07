<?php

require_once(__DIR__ . '/../../vendor/autoload.php');

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
        protected ?int          $id = null
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
        $validator = new Validator();
        return new self(
            login: $validator->getValidLogin(),
            password: password_hash($validator->getValidPassword(), PASSWORD_DEFAULT),
        );
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}