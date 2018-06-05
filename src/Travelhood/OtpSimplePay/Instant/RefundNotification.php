<?php

namespace Travelhood\OtpSimplePay\Instant;

use Travelhood\OtpSimplePay\Service;
use Travelhood\OtpSimplePay\Instant;
use Travelhood\OtpSimplePay\Util;

class RefundNotification extends Instant
{
    public function __construct(Service $service, $simplePayRef, $orderAmount, $refundAmount, $currency=null)
    {
        parent::__construct($service);
        if($currency) {
            $this->service->config->selectCurrency($currency);
        }
        $query = [
            'MERCHANT' => $this->service->config['merchant_id'],
            'ORDER_REF' => $simplePayRef,
            'ORDER_AMOUNT' => $orderAmount,
            'ORDER_CURRENCY' => $this->service->config->getCurrency(),
            'IRN_DATE' => date('Y-m-d H:i:s'),
            'AMOUNT' => $orderAmount,
        ];
        $hash = Util::hmacArray($query, $this->service->config['merchant_secret']);
        $query['ORDER_HASH'] = $hash;
        $request = $this->service->createRequest($this->service->getUrlInstantRefundNotification(), $query);
        $this->_data = $request->fetch([$this, 'parse']);
        $this->validate();
    }
}