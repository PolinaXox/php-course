<?php

class SanitizerService
{
    /**
     * Changes input data to prevent harm, injections ect.
     *
     * @param string $data
     * @return string
     */
    public static function sanitizeInputValue(string $data): string
    {
        self::sanitizeInputValueByReference($data);
        return $data;
    }

    /**
     * @param string $data
     * @return void
     */
    public static function sanitizeInputValueByReference(string &$data): void
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
    }
}