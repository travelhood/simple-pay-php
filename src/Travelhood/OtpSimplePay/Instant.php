<?php

namespace Travelhood\OtpSimplePay;

abstract class Instant extends Component
{
    /** @var array */
    protected $_data;

    protected function _getData($key)
    {
        if(array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }
        return null;
    }

    protected function _getHashKey()
    {
        return 'HASH';
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

    public function parse($raw)
    {
        $raw = substr($raw, 10, -11); // strip <epayment> tag
        $split = explode('|', $raw);
        return array_combine($this->_getKeyMap(), $split);
    }

    function validate()
    {
        $key = $this->_getHashKey();
        $data = $this->_data;
        unset($data[$key]);
        $hash = Util::hmacArray($data,$this->service->config['merchant_secret']);
        if($hash != $this->_data[$key]) {
            throw new Exception('Failed to validate hash');
        }
        if($this->getResponseCode() !== null && $this->getResponseCode() !== 1) {
            throw new InstantDeliveryNotificationException($this->getResponseText(), $this->getResponseCode());
        }
    }

    public function getSimplePayRef()
    {
        return $this->_getData('REFNO');
    }

    public function getResponseCode()
    {
        return $this->_getData('RC');
    }

    public function getResponseText()
    {
        return $this->_getData('RT');
    }

    public function getDate()
    {
        return $this->_getData('DATE');
    }
}