<?php

namespace App\PresentationLayer\InputValidator;

// singleton
final class AbsentValue
{
    private static ?self $instance = null;

    private function __construct() { }

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }
}

