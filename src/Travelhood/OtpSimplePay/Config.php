<?php

namespace Travelhood\OtpSimplePay;

use ArrayAccess;
use Travelhood\OtpSimplePay\Exception\ConfigException;

class Config implements ArrayAccess
{
    const VALID_CURRENCIES = ['USD', 'EUR', 'HUF'];

    const DEFAULT_CONFIG = [
        'timeout' => 60,
        'curl' => true,
        'live' => false,
        'log' => [
            'level' => 'DEBUG',
            'path' => __DIR__ . '/../log/simplepay.log',
            'prefix' => 'SimplePay: ',
        ],
        'merchant' => [],
    ];

    const REQUIRED_MERCHANT_KEYS = ['id', 'secret'];

    const TIMEOUT_MIN = 60;
    const TIMEOUT_MAX = 720;
    /** @var array */
    protected $_config = [];
    /** @var string */
    protected $_currency = '';

    /**
     * @param array $config
     * @throws ConfigException
     */
    public function __construct($config)
    {
        $this->setConfig($config);
    }

    /**
     * @param array $config
     * @return $this
     * @throws ConfigException
     */
    public function setConfig($config)
    {
        $this->_config = Util::mergeArray(self::DEFAULT_CONFIG, $config);
        $this->validate();
        return $this;
    }

    /**
     * @throws ConfigException
     */
    public function validate()
    {
        if ($this['timeout'] < self::TIMEOUT_MIN || $this['timeout'] > self::TIMEOUT_MAX) {
            throw new ConfigException('Timeout must be between ' . self::TIMEOUT_MIN . ' and ' . self::TIMEOUT_MAX . ' seconds');
        }
        if (!is_array($this['merchant']) || count($this['merchant']) < 1) {
            throw new ConfigException('Invalid value for merchant');
        }
        foreach ($this['merchant'] as $currency => $merchant) {
            $CUR = self::sanitizeCurrency($currency);
            if (strlen($this->_currency) < 1) {
                $this->_currency = $CUR;
            }
            if ($CUR != $currency) {
                unset($this['merchant'][$currency]);
                $this['merchant'][$CUR] = $merchant;
                $currency = $CUR;
            }
            if (!is_array($merchant)) {
                throw new ConfigException('Value for ' . $currency . ' merchant must be an array');
            }
            foreach (self::REQUIRED_MERCHANT_KEYS as $rmk) {
                if (!array_key_exists($rmk, $merchant)) {
                    throw new ConfigException('Missing key ' . $rmk . ' for ' . $currency . ' merchant');
                }
                if (strlen($merchant[$rmk]) < 1) {
                    throw new ConfigException('Invalid ' . $rmk . ' value for ' . $currency . ' merchant');
                }
            }
        }
    }

    /**
     * @param string $currency
     * @return string
     * @throws ConfigException
     */
    public static function sanitizeCurrency($currency)
    {
        $CUR = strtoupper(substr(trim($currency), 0, 3));
        if (!in_array($CUR, self::VALID_CURRENCIES)) {
            throw new ConfigException('Invalid currency: ' . $currency);
        }
        return $CUR;
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if ($offset == 'merchant_id' || $offset == 'merchant_secret') {
            $k = substr($offset, 9);
            return $this['merchant'][$this->_currency][$k];
        }
        if ($this->offsetExists($offset)) {
            return $this->_config[$offset];
        }
        return null;
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        if ($offset == 'merchant_id' || $offset == 'merchant_secret') {
            return true;
        }
        return array_key_exists($offset, $this->_config);
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws ConfigException
     */
    public function offsetSet($offset, $value)
    {
        if ($offset == 'merchant_id' || $offset == 'merchant_secret') {
            throw new ConfigException('Cannot set read-only key: ' . $offset . '');
        }
        $this->_config[$offset] = $value;
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        if ($offset == 'merchant_id' || $offset == 'merchant_secret') {
            return;
        }
        if ($this->offsetExists($offset)) {
            unset($this->_config[$offset]);
        }
    }

    /**
     * @param $config
     * @return $this
     * @throws ConfigException
     */
    public function mergeConfig($config)
    {
        if ($config instanceof self) {
            $config = $config->_config;
        }
        $this->_config = Util::mergeArray($this->_config, $config);
        $this->validate();
        return $this;
    }

    /**
     * @param string $currency
     * @return $this
     * @throws ConfigException
     */
    public function selectCurrency($currency)
    {
        $currency = self::sanitizeCurrency($currency);
        if (!array_key_exists($currency, $this['merchant'])) {
            throw new ConfigException('No such currency in config: ' . $currency);
        }
        $this->_currency = $currency;
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