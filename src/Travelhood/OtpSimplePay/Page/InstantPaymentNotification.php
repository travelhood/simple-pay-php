<?php

namespace Travelhood\OtpSimplePay\Page;

use Travelhood\OtpSimplePay\Exception\InstantPaymentNotificationException;
use Travelhood\OtpSimplePay\Page;
use Travelhood\OtpSimplePay\Util;

class InstantPaymentNotification extends Page
{
    protected $_date = null;

    public function setDate($date)
    {
        $this->_date = $date;
        return $this;
    }

    public function getMessage()
    {
        return $this->__toString();
    }

    public function getData()
    {
        return $_POST;
    }

    public function validate()
    {
        $data = $this->getData();
        $this->service->config->selectCurrency($data['CURRENCY']);
        $check = $data;
        unset($check['HASH']);
        $check = Util::flattenArray($check);
        $hash = Util::hmacArray($check, $this->service->config['merchant_secret']);
        if($hash != $data['HASH']) {
            throw new InstantPaymentNotificationException('Invalid hash received');
        }
    }

    public function confirm()
    {
        if(!$this->_date) {
            $this->_date = date('YmdHis');
        }
        $date = preg_replace('/[^\d]/', '', $this->_date);
        $check = Util::flattenArray([
            'IPN_PID' => [$this['IPN_PID'][0]],
            'IPN_PNAME' => [$this['IPN_PNAME'][0]],
            'IPN_DATE' => preg_replace('/[^\d]/','', $this['IPN_DATE']),
            'DATE' => $date,
        ]);
        $hash = Util::hmacArray($check, $this->service->config['merchant_secret']);
        return '<EPAYMENT>'.$date.'|'.$hash.'</EPAYMENT>';
    }

    public function __toString()
    {
        return $this->confirm();
    }


}