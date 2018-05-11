<?php

namespace Travelhood\OtpSimplePay;

use InvalidArgumentException;

abstract class Util
{
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

    public static function flattenArray($array, $skip=[])
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

}