<?php
// ++
namespace App\PresentationLayer\InputValidator;

use App\PresentationLayer\InputValidator\AbsentValue as AbsentValue;

class InputSanitizer
{
    /**
     * @param mixed $value
     * @return string | AbsentValue
     */
    public static function getSanitizedValue(mixed $value): string|AbsentValue
    {
        // allows to return 0, 0.0, -0.0, false, null as value
        if (self::isNumberOrBoolOrNull($value)) {
            return $value;
        }

        if (empty($value) || empty($sanitizedValue = self::sanitizeInputValue($value))) {
            return AbsentValue::instance();
        }

        return $sanitizedValue;
    }


    /**
     * @param mixed $value
     * @return bool
     */
    private static function isNumberOrBoolOrNull(mixed $value): bool
    {
        return is_int($value) || is_float($value) || is_bool($value) || is_null($value);
    }

    /**
     * Changes input data to prevent harm, injections ect.
     *
     * @param string $data
     * @return string
     */
    private static function sanitizeInputValue(string $data): string
    {
        self::sanitizeInputValueByReference($data);

        return $data;
    }

    /**
     * @param string $data
     * @return void
     */
    private static function sanitizeInputValueByReference(string &$data): void
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
    }
}

// переподумати у наступному варіанті:
// filter_var() ???
