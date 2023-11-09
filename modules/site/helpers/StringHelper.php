<?php

namespace modules\site\helpers;

class StringHelper extends \craft\helpers\StringHelper
{
    // Public Methods
    // =========================================================================

    /**
     * Returns a formatted email.
     *
     * @param $str
     * @return string
     */
    public static function formatEmail($str): string
    {
        return self::toLowerCase(self::trim($str));
    }

    /**
     * Returns a formatted phone number.
     *
     * @param $str
     * @return string
     */
    public static function formatPhone($str): string
    {
        $str = self::regexReplace($str, '[^0-9]', '');

        if (self::first($str, 1) === '1') {
            $str = self::substr($str, 1);
        }

        $str = self::substr($str, 0, 3).'-'.self::substr($str, 3, 3).'-'.self::substr($str, 6, 4);
        $str = self::trim($str, '-');

        return $str;
    }

    /**
     * Returns a formatted postal code.
     *
     * @param $str
     * @return string
     */
    public static function formatPostal($str): string
    {
        $str = self::toUpperCase($str);
        $str = self::regexReplace($str, '[^0-9A-Z]', '');
        $str = self::substr($str, 0, 3).' '.self::substr($str, 3, 3);
        $str = self::trim($str);

        return $str;
    }
}
