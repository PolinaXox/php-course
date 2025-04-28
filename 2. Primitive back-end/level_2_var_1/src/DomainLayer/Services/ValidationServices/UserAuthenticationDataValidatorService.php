<?php
namespace App\services\validationServices;

use App\PresentationLayer\DataTransferObjects\RequiredAuthenticationData as RequiredAuthenticationData;
use App\PresentationLayer\DataTransferObjects\UserInputAuthenticationDataDTO as AuthenticationData;
use App\services\validationServices\InputDataValidatorService as InputDataValidatorService;
use Exception;

class UserAuthenticationDataValidatorService
    extends InputDataValidatorService
    implements RequiredAuthenticationData
{
    private array $validAuthenticationData;

    /**
     * @throws Exception
     */
    function __construct()
    {
        parent::__construct(new AuthenticationData()->rawInputData);
        $this->validAuthenticationData = $this->getValidAuthenticationData();
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getValidAuthenticationData(): array
    {
        return [
            self::LOGIN => $this->getValidValue(self::LOGIN),
            self::PASSWORD => $this->getValidValue(self::PASSWORD)
        ];
    }

    /**
     * @return string
     */
    public function getValidLogin(): string
    {
        return $this->validAuthenticationData[self::LOGIN];
    }

    /**
     * @return string
     */
    public function getValidPassword(): string
    {
        return $this->validAuthenticationData[self::PASSWORD];
    }
}