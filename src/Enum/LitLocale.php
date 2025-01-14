<?php

namespace LiturgicalCalendar\Api\Enum;

class LitLocale
{
    public const LATIN                        = "la_VA";
    public const LATIN_PRIMARY_LANGUAGE       = "la";
    public static array $values               = [ "la", "la_VA" ];
    public static string $PRIMARY_LANGUAGE    = "la";
    public static ?array $AllAvailableLocales = null;

    /**
     * Check if the given locale is valid.
     *
     * @param mixed $value The locale value to validate.
     * @return bool True if the locale is valid, false otherwise.
     */
    public static function isValid($value)
    {
        if (null === self::$AllAvailableLocales) {
            self::$AllAvailableLocales = array_filter(\ResourceBundle::getLocales(''), function ($value) {
                return strpos($value, 'POSIX') === false;
            });
        }
        return in_array($value, self::$values) || in_array($value, self::$AllAvailableLocales);
    }

    /**
     * Check if the given array of locales is valid.
     *
     * @param array $values The array of locale values to validate.
     * @return bool True if all locales are valid, false otherwise.
     */
    public static function areValid(array $values)
    {
        foreach ($values as $value) {
            if (!self::isValid($value)) {
                return false;
            }
        }
        return true;
    }
}