<?php

require_once(__DIR__ . '/../../../vendor/autoload.php');

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