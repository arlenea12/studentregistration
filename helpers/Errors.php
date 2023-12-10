<?php 

namespace helpers;

class Errors
{
    private static $errors = [];

    public static function addError(string $key, string $message)
    {
        self::$errors[$key][] = $message;
    }

    public static function hasErrors(): bool
    {
        return !empty(self::$errors);
    }

    public static function hasError($key): bool
    {
        return !empty(self::$errors[$key]);
    }

    public static function getErrors(): array
    {
        return self::$errors;
    }

    public static function getFirstError(string $key): ?string
    {
        if (isset(self::$errors[$key][0])) {
            return self::$errors[$key][0];
        }
        return null;
    }

    public static function getFirstErrorHTML(string $key): ?string
    {
        if (isset(self::$errors[$key][0])) {
            return '<div class="invalid-feedback">' . self::$errors[$key][0] . '</div>';
        }
        return '';
    }
}
