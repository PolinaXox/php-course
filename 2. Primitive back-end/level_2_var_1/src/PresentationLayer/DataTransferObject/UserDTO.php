<?php

namespace App\PresentationLayer\DataTransferObject;

use App\PresentationLayer\InputValidator\InputValidator as InputValidator;
use App\DomainLayer\Exception\AppException as AppException;

readonly class UserDTO
{
    public string $login;
    public string $password;

    /**
     * @throws AppException
     */
    function __construct()
    {
        $validator = new InputValidator();
        $this->login = $validator->getRequiredValidValue('login');
        $this->password = $validator->getRequiredValidValue('pass');
    }
}

// не подобається: індекси для $requestBody['login'] у термінах front`a
// хочу: перевести терміни front`a у терміни back`а

// DONE _todo: об'єкт з фронт -> ДТО -> клас
// DONE _todo: validation -> before DTO ???????????
// DONE _todo: взяти усі поля вне залежності від методу запиту, перетворити на те, що потрібно для подальшої обробки


