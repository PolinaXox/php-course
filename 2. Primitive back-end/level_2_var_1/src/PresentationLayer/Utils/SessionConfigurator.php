<?php

namespace App\PresentationLayer\Utils;

class SessionConfigurator
{
    public static function configureCookies(): void
    {
        ini_set('session.cookie_secure', true);
        ini_set('session.cookie_httponly', true);
        ini_set('session.cookie_samesite', 'None');
    }
}