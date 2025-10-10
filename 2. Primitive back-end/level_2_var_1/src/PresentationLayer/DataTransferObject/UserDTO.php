<?php

namespace App\PresentationLayer\DataTransferObject;

readonly class UserDTO
{
    public string $login;
    public string $password;

    /**
     * @param array $validatedData
     */
    function __construct(array $validatedData)
    {
        $this->login = $validatedData['login'];
        $this->password = $validatedData['pass'];
    }
}


