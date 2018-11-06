<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\InstantDeliveryNotificationException;

abstract class Instant extends Component
{
    /** @var array */
    protected $_data;

    protected function _getKeyMap()
    {
        return [
            'REFNO',
            'RC',
            'RT',
            'DATE',
            'HASH',
        ];
    }

    protected function _getHashKey()
    {
        return 'HASH';
    }

    protected function _getDataKey($key)
    {
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }
        return null;
    }

    protected function _getValidResponseCode()
    {
        return 0;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param string $raw
     * @return array
     * @throws Exception
     */
    public function parse($raw)
    {
        $this->log->debug('IDN response: ' . PHP_EOL . $raw);
        $epaymentContent = null;
        if (preg_match('/\<EPAYMENT\>(.*)\<\/EPAYMENT\>/i', $raw, $matches)) {
            $epaymentContent = $matches[1];
        } else {
            $dom = new \DOMDocument();
            $dom->loadHTML($raw);
            $body = $dom->getElementsByTagName('body');
            $this->log->critical($body[0]->textContent);
            throw new Exception($body[0]->textContent);
        }
        $split = explode('|', $epaymentContent);
        return array_combine($this->_getKeyMap(), $split);
    }

    /**
     * @throws Exception
     * @throws InstantDeliveryNotificationException
     */
    function validate()
    {
        $key = $this->_getHashKey();
        $data = $this->_data;
        $this->log->debug('Validating ['.get_class($this).'] request', $data);
        unset($data[$key]);
        $hash = $this->service->hasher->hashArray($data);
        if ($hash != $this->_data[$key]) {
            $this->log->critical('Failed to validate instant hash in ['.get_class($this).']');
            throw new Exception('Failed to validate instant hash');
        }
        if ($this->getResponseCode() !== null && $this->getResponseCode() !== $this->_getValidResponseCode()) {
            $this->log->critical('['.get_class($this).'] '.$this->getResponseText().' #'.$this->getResponseCode());
            throw new InstantDeliveryNotificationException($this->getResponseText(), $this->getResponseCode());
        }
        $this->log->debug('Validated instant request');
    }

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return intval($this->_getDataKey('RC'));
    }

    /**
     * @return string
     */
    public function getResponseText()
    {
        return $this->_getDataKey('RT');
    }

    /**
     * @return string
     */
    public function getSimplePayRef()
    {
        return $this->_getDataKey('REFNO');
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->_getDataKey('DATE');
    }
}