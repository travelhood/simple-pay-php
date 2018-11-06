<?php

namespace Travelhood\OtpSimplePay\Instant;

use Travelhood\OtpSimplePay\Instant;
use Travelhood\OtpSimplePay\Request;
use Travelhood\OtpSimplePay\Service;

class RefundNotification extends Instant
{
    protected function _getValidResponseCode()
    {
        return 1;
    }

    /**
     * RefundNotification constructor.
     * @param Service $service
     * @param string $simplePayRef
     * @param int $orderAmount
     * @param int $refundAmount
     * @param string $currency
     * @throws \Travelhood\OtpSimplePay\Exception
     * @throws \Travelhood\OtpSimplePay\Exception\ConfigException
     * @throws \Travelhood\OtpSimplePay\Exception\InstantDeliveryNotificationException
     */
    public function __construct(Service $service, $simplePayRef, $orderAmount, $refundAmount=null, $currency = null)
    {
        parent::__construct($service);
        if(!$refundAmount) {
            $refundAmount = $orderAmount;
        }
        if ($currency) {
            $this->service->selectCurrency($currency);
        }
        $query = [
            'MERCHANT' => $this->service->config['merchant_id'],
            'ORDER_REF' => $simplePayRef,
            'ORDER_AMOUNT' => $orderAmount,
            'ORDER_CURRENCY' => $this->service->config->getCurrency(),
            'IRN_DATE' => date('Y-m-d H:i:s'),
            'AMOUNT' => $refundAmount,
        ];
        $hash = $this->service->hasher->hashArray($query);
        $query['ORDER_HASH'] = $hash;
        $this->log->info('IRN request', $query);
        $request = $this->service->createRequest($this->service->getUrlInstantRefundNotification(), $query);
        $request->setMethod(Request::METHOD_POST);
        $this->_data = $request->fetch([$this, 'parse']);
        $this->validate();
        $this->log->info('IRN response', $this->_data);
    }
}