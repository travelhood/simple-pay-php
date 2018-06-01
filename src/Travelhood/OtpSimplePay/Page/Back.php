<?php

namespace Travelhood\OtpSimplePay\Page;

use Travelhood\OtpSimplePay\Page;
use Travelhood\OtpSimplePay\Util;
use Travelhood\OtpSimplePay\Exception\ConfigException;
use Travelhood\OtpSimplePay\Exception\PageException;
use Travelhood\OtpSimplePay\Exception\ControlMismatchException;

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

    /**
     * @throws ConfigException
     * @throws ControlMismatchException
     * @throws PageException
     */
    public function validate()
    {
        if(!$this->offsetExists(self::KEY_ORDER_CURRENCY)) {
            throw new PageException(self::KEY_ORDER_CURRENCY.' must be passed along in the url');
        }
        if($this->offsetExists(self::KEY_CONTROL_HASH)) {
            $fullUrl = 'http'.((array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS']) ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $fullUrl = preg_replace("/\&ctrl\=[a-zA-Z0-9]+$/", '', $fullUrl);
            $this->service->config->selectCurrency($this[self::KEY_ORDER_CURRENCY]);
            $hash = Util::hmac(strlen($fullUrl).$fullUrl,$this->service->config['merchant_secret']);
            if($hash != $this[self::KEY_CONTROL_HASH]) {
                throw new ControlMismatchException('Control variable mismatch!');
            }
        }
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
        if($this->hasError()) {
            if($this->offsetExists(self::KEY_ERROR)) {
                return $this[self::KEY_ERROR];
            }
            return $this[self::KEY_RETURN_TEXT];
        }
        return parent::getError();
    }

    /**
     * @return string|null
     */
    public function getOrderRef()
    {
        return $this[self::KEY_ORDER_REF];
    }

    /**
     * @return string|null
     */
    public function getOrderDate()
    {
        return $this[self::KEY_ORDER_DATE];
    }

    public function getPaymentNumber()
    {
        return $this[self::KEY_PAYMENT_NUMBER];
    }

    public function __toString()
    {
        $msg = '';
        if($this->hasError()) {
            $msg.= 'An error has occurred: ' . $this->getError();
            $msg.= '<br/>' . PHP_EOL;
        }
        else {
            $msg.= 'Successful transaction!';
            $msg.= '<br/>' . PHP_EOL;
            $msg.= "Payment number (SimplePay): ".$this->getPaymentNumber() . '<br/>' . PHP_EOL;
        }
        $msg.= "Order reference: ".$this->getOrderRef() . '<br/>' . PHP_EOL;
        if($this->getOrderDate()) {
            $msg .= "Order date: " . $this->getOrderDate() . '<br/>' . PHP_EOL;
        }
        $msg.= '<pre>';
        $msg.= json_encode($this->toArray(), JSON_PRETTY_PRINT);
        $msg.= '</pre>';
        return $msg;
    }
}