<?php

require_once (__DIR__ . '/../../../vendor/autoload.php');

use UserInputRegistrationDataDTO as RegistrationData;

class UserRegistrationDataValidatorService
    extends InputDataValidatorService
    implements RequiredRegistrationData
{
    private array $validRegistrationData;

    /**
     * @throws Exception
     */
    function __construct()
    {
        parent::__construct(new RegistrationData()->rawInputData);
        $this->validRegistrationData = $this->getValidRegistrationData();
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getValidRegistrationData() : array {
        return [
            self::LOGIN => $this->getUniqueValidLogin(),
            self::PASSWORD => parent::getValidValue(self::PASSWORD),
        ];
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getUniqueValidLogin() : string {
        $login = parent::getValidValue(self::LOGIN);
        $this->ensureUserLoginIsUnique($login);
        return $login;
    }

    /**
     * @return string
     */
    public function getValidLogin() : string {
        return $this->validRegistrationData[self::LOGIN];
    }

    /**
     * @return string
     */
    public function getValidPassword() : string {
        return $this->validRegistrationData[self::PASSWORD];
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
