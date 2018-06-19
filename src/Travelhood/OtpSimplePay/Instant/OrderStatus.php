<?php

namespace Travelhood\OtpSimplePay\Instant;

use Travelhood\OtpSimplePay\Exception\InstantOrderStatusException;
use Travelhood\OtpSimplePay\Instant;
use Travelhood\OtpSimplePay\Service;

class OrderStatus extends Instant
{
    public function __construct(Service $service, $orderRef, $currency = null)
    {
        parent::__construct($service);
        if ($currency) {
            $this->service->config->selectCurrency($currency);
        }
        $data = [
            'MERCHANT' => $this->service->config['merchant_id'],
            'REFNOEXT' => $orderRef,
        ];
        $hash = $this->service->hasher->hashArray($data);
        $data['HASH'] = $hash;
        $request = $this->service->createRequest($this->service->getUrlInstantOrderStatus(), $data);
        $request->setMethod('POST');
        $this->log->info('IOS request', $data);
        $this->_data = $request->fetch();
        if (!is_array($this->_data) || !array_key_exists('ORDER_STATUS', $this->_data)) {
            throw new InstantOrderStatusException('Failed to parse response');
        }
        $this->validate();
        $this->log->info('IOS response', $this->_data);
    }

    public function getOrderRef()
    {
        return $this->_getDataKey('REFNOEXT');
    }

    public function getOrderStatus()
    {
        return $this->_getDataKey('ORDER_STATUS');
    }

    public function getPayMethod()
    {
        return $this->_getDataKey('PAYMETHOD');
    }

    public function getDate()
    {
        return $this->_getDataKey('');
    }

}