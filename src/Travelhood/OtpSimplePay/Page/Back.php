<?php

namespace Travelhood\OtpSimplePay\Page;

use Travelhood\OtpSimplePay\Exception\ConfigException;
use Travelhood\OtpSimplePay\Exception\ControlMismatchException;
use Travelhood\OtpSimplePay\Exception\PageException;
use Travelhood\OtpSimplePay\Page;

class Back extends Page
{
    const KEY_ERROR = 'err';
    const KEY_ORDER_REF = 'order_ref';
    const KEY_ORDER_DATE = 'date';
    const KEY_ORDER_CURRENCY = 'order_currency';
    const KEY_RETURN_CODE = 'RC';
    const KEY_RETURN_TEXT = 'RT';
    const KEY_PAYMENT_NUMBER = 'payrefno';
    const KEY_CONTROL_HASH = 'ctrl';

    public function getData()
    {
        return $_GET;
    }

    /**
     * @throws ConfigException
     * @throws ControlMismatchException
     * @throws PageException
     */
    public function validate()
    {
        if (!$this->offsetExists(self::KEY_ORDER_CURRENCY)) {
            throw new PageException(self::KEY_ORDER_CURRENCY . ' must be passed along in the url');
        }
        if ($this->offsetExists(self::KEY_CONTROL_HASH)) {
            $port = $_SERVER['SERVER_PORT'];
            if ($port == 80 || $port == 443) {
                $port = '';
            } else {
                $port = ':' . $port;
            }
            $protocol = 'http';
            if (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER)) {
                $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
            } elseif (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS']) {
                $protocol = 'https';
            }
            $fullUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $port . $_SERVER['REQUEST_URI'];
            $checkUrl = preg_replace("/\&" . self::KEY_CONTROL_HASH . "\=.+$/", '', $fullUrl);
            $this->service->config->selectCurrency($this[self::KEY_ORDER_CURRENCY]);
            $hash = $this->service->hasher->hashString($checkUrl, true);
            if ($hash != $this[self::KEY_CONTROL_HASH]) {
                throw new ControlMismatchException('Control variable mismatch! [' . $fullUrl . ']');
            }
        }
    }

    /**
     * @return string
     */
    public function getOrderCurrency()
    {
        return $this[self::KEY_ORDER_CURRENCY];
    }

    public function __toString()
    {
        $msg = '';
        if ($this->hasError()) {
            $msg .= 'An error has occurred: ' . $this->getError();
            $msg .= '<br/>' . PHP_EOL;
        } else {
            $msg .= 'Successful transaction!';
            $msg .= '<br/>' . PHP_EOL;
            $msg .= "Payment number (SimplePay): " . $this->getSimplePayRef() . '<br/>' . PHP_EOL;
        }
        $msg .= "Order reference: " . $this->getOrderRef() . '<br/>' . PHP_EOL;
        if ($this->getOrderDate()) {
            $msg .= "Order date: " . $this->getOrderDate() . '<br/>' . PHP_EOL;
        }
        return $msg;
    }

    public function hasError()
    {
        return (
            $this->offsetExists(self::KEY_ERROR) && strlen($this[self::KEY_ERROR]) > 0
            ||
            $this->offsetExists(self::KEY_RETURN_CODE) && intval($this[self::KEY_RETURN_CODE]) > 1
        );
    }

    public function getError()
    {
        if ($this->hasError()) {
            if ($this->offsetExists(self::KEY_ERROR)) {
                return $this[self::KEY_ERROR];
            }
            return $this[self::KEY_RETURN_TEXT];
        }
        return parent::getError();
    }

    public function getSimplePayRef()
    {
        return $this[self::KEY_PAYMENT_NUMBER];
    }

    /**
     * @return string
     */
    public function getOrderRef()
    {
        return $this[self::KEY_ORDER_REF];
    }

    /**
     * @return string
     */
    public function getOrderDate()
    {
        return $this[self::KEY_ORDER_DATE];
    }
}