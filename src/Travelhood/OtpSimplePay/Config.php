<?php

namespace Travelhood\OtpSimplePay;

use ArrayObject;
use ArrayIterator;
use Travelhood\OtpSimplePay\Exception\ConfigException;

class Config extends ArrayObject
{
    const CURRENCIES = ['USD', 'EUR', 'HUF'];
    const KEYS_REQUIRED = ['MERCHANT', 'SECRET_KEY'];
    const DEFAULTS = [
        'BASE_URL' => "https://secure.simplepay.hu/payment/", //LIVE system
        'SANDBOX_URL' => "https://sandbox.simplepay.hu/payment/", //SANDBOX system
        'LU_URL' => "order/lu.php",   //relative to BASE_URL
        'ALU_URL' => "order/alu.php", //relative to BASE_URL
        'IDN_URL' => "order/idn.php", //relative to BASE_URL
        'IRN_URL' => "order/irn.php", //relative to BASE_URL
        'IOS_URL' => "order/ios.php", //relative to BASE_URL
        'OC_URL' => "order/tokens/"   //relative to BASE_URL
    ];

    /** @var string */
    protected $_currency;

    /**
     * @param array $config
     */
    public function __construct($config=[])
    {
        parent::__construct($config, ArrayObject::ARRAY_AS_PROPS, ArrayIterator::class);
    }

    /**
     * @throws ConfigException
     */
    public function validate()
    {
        foreach(self::KEYS_REQUIRED as $key) {
            if(!$this->offsetExists($key)) {
                throw new ConfigException('Missing required config: '.$key);
            }
        }
    }

    /**
     * @param string $currency
     * @return $this
     * @throws ConfigException
     */
    public function setCurrency($currency)
    {
        if(!in_array($currency, self::CURRENCIES)) {
            throw new ConfigException('Invalid currency: '.$currency);
        }
        $this->_currency = $currency;
        foreach(['MERCHANT', 'SECRET_KEY'] as $k) {
            $ck = $currency.'_'.$k;
            if(!$this->offsetExists($ck)) {
                throw new ConfigException('Missing config: '.$ck);
            }
        }
        $this['MERCHANT'] = $this[$currency.'_MERCHANT'];
        $this['SECRET_KEY'] = $this[$currency.'_SECRET_KEY'];
        $this->validate();
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->_currency;
    }
}