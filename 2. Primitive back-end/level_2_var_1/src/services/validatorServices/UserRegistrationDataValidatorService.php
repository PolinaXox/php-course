<?php

require_once (__DIR__ . '/../../../vendor/autoload.php');

use UserInputRegistrationDataDTO as RegistrationData;

class UserRegistrationDataValidatorService
    extends InputDataValidatorService
    implements RequiredRegistrationData
{
    function __construct()
    {
        parent::__construct(new RegistrationData()->rawInputData);
    }

    /**
     * @return array
     * @throws Exception
     */
    static public function getValidRegistrationData(): array
    {
        $validator = new self();
        return $validator->getValidatedData();
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getValidatedData(): array
    {
        return [
            self::LOGIN => $this->getValidLogin(),
            self::PASSWORD => $this->getValidValue(self::PASSWORD),
        ];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getValidLogin(): string
    {
        $login = $this->getValidValue(self::LOGIN);
        $this->ensureUserLoginIsUnique($login);
        return $login;
    }

    /**
     * @param string $login
     * @return void
     * @throws Exception
     */
    private function ensureUserLoginIsUnique(string $login): void
    {
        if (new UserDAO()->findByLogin($login)) {
            throw new Exception('This login is already in use. Please, try another one.', 400);
        }
    }
}