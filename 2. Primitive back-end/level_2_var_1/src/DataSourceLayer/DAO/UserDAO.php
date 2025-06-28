<?php

namespace App\DataSourceLayer\DAO;

use App\DataSourceLayer\DataMapper\UserMapper as UserMapper;
use App\DataSourceLayer\ServiceDB\FileService as FileService;
use App\DomainLayer\Entity\User as User;
use App\DomainLayer\Exception\AppException as AppException;
use App\DomainLayer\Exception\AppExceptionsList as AppExceptionsList;
use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;

class UserDAO
{
    private string $filePath = __DIR__ . '/../../../FileDB/registered_users.json';
    private array $users;

    /**
     * @throws AppException
     */
    public function __construct()
    {
        new FileService()->ensureFileExists($this->filePath);
        $this->users = json_decode(file_get_contents($this->filePath), true) ?? [];
    }

    /**
     * @param User $user
     * @return bool
     */
    public function save(User $user): bool
    {
        $key = $user->login; // ?????????????????
        $this->users[$key] = new UserMapper()->mapToDatabaseRecord($user);

        return $this->saveChangesToDB();
    }

    /**
     * @return bool
     */
    private function saveChangesToDB(): bool
    {
        return file_put_contents(
            $this->filePath,
            json_encode($this->users, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK)
        );
    }

    /**
     * @param string $login
     * @return void
     * @throws AppException
     */
    public function ensureUserLoginIsUnique(string $login): void
    {
        if ($this->findByLogin($login) instanceof AbsentValue) {
            return;
        }

        throw AppException::fromEnum(AppExceptionsList::LoginIsNotUnique);
    }

    /**
     * @param string $login
     * @return User|AbsentValue
     * @throws AppException
     */
    public function findByLogin(string $login): User|AbsentValue
    {
        return array_key_exists($login, $this->users) ?
            new UserMapper()->mapToEntity($this->users[$login]) :
            AbsentValue::instance();
    }
}