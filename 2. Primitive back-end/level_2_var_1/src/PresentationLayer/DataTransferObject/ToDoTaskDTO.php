<?php

namespace App\PresentationLayer\DataTransferObject;

use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;
use App\PresentationLayer\InputValidator\InputValidator as InputValidator;

readonly class ToDoTaskDTO
{
    public int|AbsentValue $id;
    public string|AbsentValue $text;
    public bool|AbsentValue $checked;

    function __construct()
    {
        $validator = new InputValidator();
        $this->id = $validator->getValidValue('id');
        $this->text = $validator->getValidValue('text');
        $this->checked = $validator->getValidValue('checked');



    }

    // valid option:
    // id - text - checked
    // addItem:
    //      id      : AV     - немає і не може бути, встановлюється нове
    //      text    : !empty - порожнє поле не пропускає фронт
    //      checked : AV     - немає і не може бути, встановлюється checked==false
    //
    // changeItem:      id - anyValue  - checked==someValue
    //      id      : той, що існує - МАЄ БУТИ
    //      text    : будь-що       - МАЄ БУТИ !empty (за логікою додавання, але фронт пропускає порожнє поле )
    //      checked : МАЄ БУТИ      -
    //
    //
    // deleteItem       id - AV        - AV
}

// не подобається: індекси для $requestBody['login'] у термінах front`a
// хочу: перевести терміни front`a у терміни back`а


