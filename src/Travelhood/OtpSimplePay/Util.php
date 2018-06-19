<?php

namespace Travelhood\OtpSimplePay;

abstract class Util
{
    const CHAR_BLACKLIST = ["'", "\\", "\""];

    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function mergeArray($array1, $array2)
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::mergeArray($merged[$key], $value);
            } else if (is_numeric($key)) {
                if (!in_array($value, $merged)) {
                    $merged[] = $value;
                }
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }

    /**
     * @param string $str
     * @param string $blacklist
     * @return string
     */
    public static function cleanString($str, $blacklist = '')
    {
        if ($blacklist == '') {
            $blacklist = self::CHAR_BLACKLIST;
        }
        return str_replace($blacklist, '', $str);
    }

    /**
     * UNSAFE!
     * @param string $template
     * @param array $dictionary
     * @return string
     */
    public static function interpolateString($template, array $dictionary)
    {
        foreach ($dictionary as $k => $v) {
            $template = str_replace('%{' . $k . '}', $v, $template);
        }
        return $template;
    }

}