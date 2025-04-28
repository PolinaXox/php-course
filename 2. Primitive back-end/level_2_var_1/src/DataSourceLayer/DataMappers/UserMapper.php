<?php
namespace App\ORM\dataMappers;

use App\entities\User as User;

class UserMapper
{
    /**
     * @param string|null $strDB
     * @return User|null
     */
    static public function mapToObject(?string $strDB): ?User
    {
        return $strDB ? unserialize($strDB) : null;
    }

    /**
     * @param User $user
     * @return string
     */
    static public function mapToStringDB(User $user): string
    {
        return serialize($user);
    }
}