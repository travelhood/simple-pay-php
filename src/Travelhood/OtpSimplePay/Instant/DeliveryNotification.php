<?php

namespace Travelhood\OtpSimplePay\Instant;

use Travelhood\OtpSimplePay\Instant;
use Travelhood\OtpSimplePay\Request;
use Travelhood\OtpSimplePay\Service;


class DeliveryNotification extends Instant
{
    protected function _getValidResponseCode()
    {
        return 1;
    }

    /**
     * @param Service $service
     * @param string $simplePayRef
     * @param float $amount
     * @param string $currency (optional)
     * @throws \Travelhood\OtpSimplePay\Exception
     * @throws \Travelhood\OtpSimplePay\Exception\ConfigException
     * @throws \Travelhood\OtpSimplePay\Exception\InstantDeliveryNotificationException
     * @throws \Travelhood\OtpSimplePay\Exception\RequestException
     */
    public function __construct(Service $service, $simplePayRef, $amount, $currency = null)
    {
        parent::__construct($service);
        if ($currency) {
            $this->service->selectCurrency($currency);
        }
        $query = [
            'MERCHANT' => $this->service->config['merchant_id'],
            'ORDER_REF' => $simplePayRef,
            'ORDER_AMOUNT' => $amount,
            'ORDER_CURRENCY' => $this->service->config->getCurrency(),
            'IDN_DATE' => date('Y-m-d H:i:s'),
        ];
        $hash = $this->service->hasher->hashArray($query);
        $query['ORDER_HASH'] = $hash;
        $this->log->info('IDN request', $query);
        $request = $this->service->createRequest($this->service->getUrlInstantDeliveryNotification(), $query);
        $request->setMethod(Request::METHOD_POST);
        $this->_data = $request->fetch([$this, 'parse']);
        $this->log->info('IDN response', $this->_data);
        $this->validate();
    }

}