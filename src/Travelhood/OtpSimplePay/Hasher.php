<?php

namespace Travelhood\OtpSimplePay;

use InvalidArgumentException;

class Hasher extends Component
{
    /**
     * @param array $array
     * @return string
     */
    public function hashArray(array &$array)
    {
        $flat = self::flattenArray($array);
        if (count($flat) == 0) {
            throw new InvalidArgumentException('Empty $array parameter provided');
        }
        $serial = '';
        foreach ($flat as &$field) {
            if (is_array($field)) {
                throw new InvalidArgumentException('No multi-dimensional $array is allowed!');
            }
            $serial .= strlen(stripslashes($field)) . $field;
        }
        return $this->hashString($serial);
    }

    /**
     * @param array $array
     * @return array
     */
    public static function flattenArray(array &$array)
    {
        $flat = [];
        foreach ($array as $name => &$item) {
            if (is_array($item)) {
                foreach ($item as &$subItem) {
                    $flat[] = $subItem;
                }
            } else {
                $flat[] = $item;
            }
        }
        return $flat;
    }

    /**
     * @param string $string
     * @param bool $prefixLength
     * @return string
     */
    public function hashString($string, $prefixLength = false)
    {
        return hash_hmac('md5', ($prefixLength ? strlen($string) : '') . $string, $this->service->config['merchant_secret']);
    }
}