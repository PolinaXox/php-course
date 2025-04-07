<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

use UserAuthenticationDataValidatorService as Validator;

class UserAuthenticationService
{
    private Validator $validator;

    private function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * @return User
     * @throws Exception
     */
    static public function getRegisteredUser(): User
    {
        $authenticator = new self();
        return $authenticator->getUserFromDB();
    }

    /**
     * @return User
     * @throws Exception
     */
    private function getUserFromDB(): User
    {
        $user = new UserDAO()->findByLogin($this->validator->getValidLogin());
        $this->ensureUserIsExist($user);
        $this->ensurePasswordMatched($user->getPassword());
        return $user;
    }

    /**
     * @param User|null $user
     * @return void
     * @throws Exception
     */
    private function ensureUserIsExist(?User $user): void
    {
        if (!$user) {
            throw new Exception('Authentication failed. Would you to register?', 400);
        }
    }

    /**
     * @param string $passwordFromDB
     * @return void
     * @throws Exception
     */
    private function ensurePasswordMatched(string $passwordFromDB): void
    {
        if (!password_verify($this->validator->getValidPassword(), $passwordFromDB)) {
            throw new Exception('Authentication failed. Forgot password?', 400);
        }
    }
}