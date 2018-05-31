<?php

namespace Travelhood\OtpSimplePay;

use InvalidArgumentException;

abstract class Util
{
    const CHAR_BLACKLIST = ["'", "\\", "\""];

    /**
     * Generates HMAC hash from string
     * @param string $data
     * @param string $key
     * @return string
     */
    public static function hmac($data, $key)
    {
        if (strlen($data) == 0) {
            throw new InvalidArgumentException('hmac() $data is empty');
        }
        if (strlen($key) == 0) {
            throw new InvalidArgumentException('hmac() $key is empty');
        }
        return hash_hmac('md5', $data, trim($key));
    }

    /**
     * Generates HMAC hash from array
     * @param array $array
     * @param string $key
     * @return string
     */
    public static function hmacArray($array, $key)
    {
        if (count($array) == 0) {
            throw new InvalidArgumentException('çreateHashString() $array is empty, so we can not generate hash string');
        }
        $serial = '';
        foreach ($array as $field) {
            if (is_array($field)) {
                throw new InvalidArgumentException('çreateHashString() No multi-dimension array allowed!');
            }
            $serial .= strlen(stripslashes($field)) . $field;
        }
        return self::hmac($serial, $key);
    }

    /**
     * @param array $array
     * @param array $skip
     * @return array
     */
    public static function flattenArray($array, $skip = [])
    {
        $flat = [];
        foreach ($array as $name => $item) {
            if (!in_array($name, $skip)) {
                if (is_array($item)) {
                    foreach ($item as $subItem) {
                        $flat[] = $subItem;
                    }
                } elseif (!is_array($item)) {
                    $flat[] = $item;
                }
            }
        }
        return $flat;
    }

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
        foreach($dictionary as $k=>$v) {
            $template = str_replace('%{'.$k.'}', $v, $template);
        }
        return $template;
    }

}