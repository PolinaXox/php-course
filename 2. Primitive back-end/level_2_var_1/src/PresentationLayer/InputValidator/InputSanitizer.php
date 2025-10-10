<?php

namespace App\PresentationLayer\InputValidator;

class InputSanitizer
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function getSanitizedValue(mixed $value): mixed
    {
        return is_string($value) ? $this->sanitizeString($value) : $value;
    }

    /**
     * Changes input data to prevent harm, injections ect.
     *
     * @param string $data
     * @return string
     */
    private function sanitizeString(string $data): string
    {
        $this->sanitizeStringByReference($data);

        return $data;
    }

    /**
     * @param string $data
     * @return void
     */
    private function sanitizeStringByReference(string &$data): void
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data,ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
    }
}

