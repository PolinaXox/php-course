<?php
// -
// no usage

namespace App\ORM\DAO;

use App\DataSourceLayer\servicesDB\FileService as ValidatorDB;
use App\entities\User as User;
use App\ORM\dataMappers\UserMapper as userMapper;
use Exception;

class UserDAO
{
    const string REGISTERED_USERS_FILE = __DIR__ . '/../../../FileDB/registered_users.txt';

    /**
     * @param User $user
     * @return bool
     * @throws Exception
     */
    public function save(User $user): bool
    {
        ValidatorDB::ensureFileExists(self::REGISTERED_USERS_FILE);
        return file_put_contents(
            self::REGISTERED_USERS_FILE,
            UserDAO . phpUserMapper::mapToStringDB($user) . PHP_EOL,
            FILE_APPEND
        );
    }

    /**
     * @param int $id
     * @return User|null
     * @throws Exception
     */
    public function find(int $id): ?User
    {
        return $this->findFirstByOneCriteria('id', $id);
    }

    /**
     * @param string $login
     * @return User|null
     * @throws Exception
     */
    public function findByLogin(string $login): ?User
    {
        return $this->findFirstByOneCriteria('login', $login);
    }

    /**
     * @param string $criteria
     * @param string $value
     * @return User|null
     * @throws Exception
     */
    private function findFirstByOneCriteria(string $criteria, string $value): ?User
    {
        ValidatorDB::ensureFileExists(self::REGISTERED_USERS_FILE);
        $file = fopen(self::REGISTERED_USERS_FILE, 'r');
        $user = $this->findFirstInFile($file, $criteria, $value);
        fclose($file);
        return $user ?? null;
    }

    /**
     * @param $file /resource/
     * @param string $criteria
     * @param string $value
     * @return User|null
     */
    private function findFirstInFile($file, string $criteria, string $value): ?User
    {
        while (!feof($file)) {
            if ($user = self::findInLine(fgets($file), $criteria, $value)) {
                return $user;
            }
        }
        return null;
    }

    /**
     * @param string $line
     * @param string $criteria
     * @param string $value
     * @return User|null
     */
    private function findInLine(string $line, string $criteria, string $value): ?User
    {
        $user = UserMapper::mapToObject(trim($line)) ?? null;
        if (!$user || ($user->toArray()[$criteria] ?? null) !== $value) {
            return null;
        }
        return $user;
    }
}