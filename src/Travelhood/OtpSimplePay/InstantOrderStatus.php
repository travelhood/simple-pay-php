<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\InstantOrderStatusException;

class InstantOrderStatus extends Instant
{
    public function __construct(Service $service, $orderRef, $currency=null)
    {
        parent::__construct($service);
        if($currency) {
            $this->service->config->selectCurrency($currency);
        }
        $data = [
            'MERCHANT' => $this->service->config['merchant_id'],
            'REFNOEXT' => $orderRef,
        ];
        $secret = $this->service->config['merchant_secret'];
        $hash = Util::hmacArray($data, $secret);
        $data['HASH'] = $hash;
        $request = $this->service->createRequest($this->service->getUrlInstantOrderStatus(), $data);
        $request->setMethod('POST');
        $this->_data = $request->fetch();
        if(!is_array($this->_data) || !array_key_exists('ORDER_STATUS', $this->_data)) {
            throw new InstantOrderStatusException('Failed to parse response');
        }
        $this->validate();
    }

    public function getOrderRef()
    {
        return $this->_getData('REFNOEXT');
    }

    public function getOrderStatus()
    {
        return $this->_getData('ORDER_STATUS');
    }

    public function getPayMethod()
    {
        return $this->_getData('PAYMETHOD');
    }

}