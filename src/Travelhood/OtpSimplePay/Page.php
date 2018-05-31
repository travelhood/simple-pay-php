<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\ControlMismatchException;
use Travelhood\OtpSimplePay\Exception\PageException;

abstract class Page extends Component
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
     * @return string
     */
    abstract public function getMessage();

    public function __construct(Service $service)
    {
        parent::__construct($service);
        $this->validate();
    }

    public function validate()
    {
        if(!array_key_exists(self::KEY_ORDER_CURRENCY, $_GET)) {
            throw new PageException(self::KEY_ORDER_CURRENCY.' must be passed along in the url');
        }
        $fullUrl = 'http'.((array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS']) ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $fullUrl = preg_replace("/\&ctrl\=[a-zA-Z0-9]+$/", '', $fullUrl);
        $this->service->config->selectCurrency($_GET[self::KEY_ORDER_CURRENCY]);
        $hash = Util::hmac(strlen($fullUrl).$fullUrl,$this->service->config['merchant_secret']);
        if($hash != $_GET[self::KEY_CONTROL_HASH]) {
            throw new ControlMismatchException('Control variable mismatch!');
        }
    }

    public function toArray()
    {
        return $_GET;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return (
            array_key_exists(self::KEY_ERROR, $_GET) && strlen($_GET[self::KEY_ERROR])>0
            ||
            array_key_exists(self::KEY_RETURN_CODE, $_GET) && intval($_GET[self::KEY_RETURN_CODE]) > 1
        );
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        if($this->hasError()) {
            if(array_key_exists(self::KEY_ERROR, $_GET)) {
                return $_GET[self::KEY_ERROR];
            }
            return $_GET[self::KEY_RETURN_TEXT];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getOrderRef()
    {
        if(array_key_exists(self::KEY_ORDER_REF, $_GET)) {
            return $_GET[self::KEY_ORDER_REF];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getOrderDate()
    {
        if(array_key_exists(self::KEY_ORDER_DATE, $_GET)) {
            return $_GET[self::KEY_ORDER_DATE];
        }
        return null;
    }

    public function getPaymentNumber()
    {
        if(array_key_exists(self::KEY_PAYMENT_NUMBER, $_GET)) {
            return $_GET[self::KEY_PAYMENT_NUMBER];
        }
        return null;
    }

    public function __toString()
    {
        $msg = '';
        if($this->hasError()) {
            $msg.= 'An error has occurred: ' . $this->getError();
            $msg.= '<br/>' . PHP_EOL;
        }
        else {
            $msg.= $this->getMessage();
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