<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\InstantDeliveryNotificationException;

class InstantDeliveryNotification extends Component
{
    /** @var array */
    protected $_data = [];

    protected function _getData($key)
    {
        if(array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }
        return null;
    }

    public function __construct(Service $service, $simplePayRef, $amount, $currency=null)
    {
        parent::__construct($service);
        if($currency) {
            $this->service->config->selectCurrency($currency);
        }
        $query = [
            'MERCHANT' => $this->service->config['merchant_id'],
            'ORDER_REF' => $simplePayRef,
            'ORDER_AMOUNT' => $amount,
            'ORDER_CURRENCY' => $this->service->config->getCurrency(),
            'DATE_IDN' => date('Y-m-d H:i:s'),
        ];
        $hash = Util::hmacArray($query, $this->service->config['merchant_secret']);
        $query['ORDER_HASH'] = $hash;
        $request = $this->service->createRequest($this->service->getUrlInstantDeliveryNotification(), $query);
        $this->_data = $request->fetch(function($raw) {
            preg_match('/\<epayment\>([^|]+)|([^|]+)|([^|]+)|([^|]+)|([^|]+)\<\/epayment\>/', $raw, $matches);
            return [
                'ORDER_REF' => $matches[1],
                'RC' => intval($matches[2]),
                'RT' => $matches[3],
                'DATE_IDN' => $matches[4],
                'HASH' => $matches[5],
            ];
        });
        $this->validate();
    }

    public function validate()
    {
        $check = $this->_data;
        unset($check['HASH']);
        $hash = Util::hmacArray($check, $this->service->config['merchant_secret']);
        if($hash != $this->_getData('HASH')) {
            throw new InstantDeliveryNotificationException('Failed to validate hash');
        }
        if($this->getResponseCode() != 1) {
            throw new InstantDeliveryNotificationException($this->getResponseText(), $this->getResponseCode());
        }
    }

    public function getSimplePayRef()
    {
        return $this->_getData('ORDER_REF');
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
        return $this->_getData('DATE_IDN');
    }

}