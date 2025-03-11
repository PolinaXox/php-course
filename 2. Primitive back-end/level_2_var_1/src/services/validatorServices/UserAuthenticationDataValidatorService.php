<?php

require_once (__DIR__ . '/../../../vendor/autoload.php');

use UserInputAuthenticationDataDTO as AuthenticationData;

class UserAuthenticationDataValidatorService
    extends InputDataValidatorService
    implements RequiredAuthenticationData
{
    function __construct()
    {
        parent::__construct(new AuthenticationData()->rawInputData);
    }

    /**
     * @return User
     * @throws Exception
     */
    static public function getRegisteredUser(): User {
        $validator = new self();
        return $validator->getUserFromDB();
    }

    /**
     * @return User
     * @throws Exception
     */
    private function getUserFromDB(): User
    {
        $user = new UserDAO()->findByLogin($this->getValidValue(self::LOGIN));
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
     * @param string $inputPassword
     * @return void
     * @throws Exception
     */
    private function ensurePasswordMatched(string $inputPassword): void
    {
        if ($this->getValidValue(self::PASSWORD) !== $inputPassword) {
            throw new Exception('Authentication failed. Forgot password?', 400);
        }
    }
}