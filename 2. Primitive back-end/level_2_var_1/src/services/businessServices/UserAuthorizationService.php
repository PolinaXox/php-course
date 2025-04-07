<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

readonly class UserAuthorizationService
{
    function __construct(
        private User $registerUser
    )
    {
    }

    /**
     * @param User $registerUser
     * @return void
     * @throws Exception
     */
    public static function authorize(User $registerUser): void
    {
        $authorizer = new self($registerUser);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new Exception("Authorization failed", 401);
        }

        $_SESSION['userFile'] = $authorizer->getUserFileName();
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getDataForAuthorization(): array
    {
        FileService::ensureUserFileExistOrCreate($this->registerUser);

        return [
            'userToDoListFileName' => ToDoListDAO::getUserToDoListFileName($this->registerUser),
        ];
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getUserFileName(): string {
        return self::getDataForAuthorization()['userToDoListFileName'];
    }
}