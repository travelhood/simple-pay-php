<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\InstantDeliveryNotificationException;

class InstantDeliveryNotification extends Instant
{
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
        $this->_data = $request->fetch([$this, 'parse']);
        $this->validate();
    }

}