<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\InstantDeliveryNotificationException;

abstract class Instant extends Component
{
    /** @var array */
    protected $_data;

    public function getData()
    {
        return $this->_data;
    }

    public function parse($raw)
    {
        $dom = new \DOMDocument();
        $dom->loadHTML($raw);
        $epayment = $dom->getElementsByTagName('epayment');
        if (count($epayment) < 1) {
            $body = $dom->getElementsByTagName('body');
            $this->log->critical($body[0]->textContent);
            throw new Exception($body[0]->textContent);
        }
        //$raw2 = substr($raw, 10, -11); // strip <epayment> tag
        $split = explode('|', $epayment[0]->textContent);
        return array_combine($this->_getKeyMap(), $split);
    }

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

    function validate()
    {
        $key = $this->_getHashKey();
        $data = $this->_data;
        $this->log->debug('Validating instant request', $data);
        unset($data[$key]);
        $hash = $this->service->hasher->hashArray($data);
        if ($hash != $this->_data[$key]) {
            $this->log->critical('Failed to validate instant hash');
            throw new Exception('Failed to validate instant hash');
        }
        if ($this->getResponseCode() !== null && $this->getResponseCode() !== 1) {
            $this->log->critical($this->getResponseText().' #'.$this->getResponseCode());
            throw new InstantDeliveryNotificationException($this->getResponseText(), $this->getResponseCode());
        }
        $this->log->debug('Validated instant request');
    }

    protected function _getHashKey()
    {
        return 'HASH';
    }

    public function getResponseCode()
    {
        return $this->_getDataKey('RC');
    }

    protected function _getDataKey($key)
    {
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }
        return null;
    }

    public function getResponseText()
    {
        return $this->_getDataKey('RT');
    }

    public function getSimplePayRef()
    {
        return $this->_getDataKey('REFNO');
    }

    public function getDate()
    {
        return $this->_getDataKey('DATE');
    }
}