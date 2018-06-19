<?php

namespace Travelhood\OtpSimplePay\Page;

use Travelhood\OtpSimplePay\Exception\InstantPaymentNotificationException;
use Travelhood\OtpSimplePay\Page;

class PaymentNotification extends Page
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

    public function __toString()
    {
        return $this->confirm();
    }

    public function confirm()
    {
        if (!$this->_date) {
            $this->_date = date('YmdHis');
        }
        $date = preg_replace('/[^\d]/', '', $this->_date);
        $check = [
            'IPN_PID' => [$this['IPN_PID'][0]],
            'IPN_PNAME' => [$this['IPN_PNAME'][0]],
            'IPN_DATE' => preg_replace('/[^\d]/', '', $this['IPN_DATE']),
            'DATE' => $date,
        ];
        $hash = $this->service->hasher->hashArray($check);
        return '<EPAYMENT>' . $date . '|' . $hash . '</EPAYMENT>';
    }

    public function validate()
    {
        $data = $this->getData();
        if (!array_key_exists('CURRENCY', $data)) {
            throw new InstantPaymentNotificationException('Invalid request received');
        }
        $this->service->selectCurrency($data['CURRENCY']);
        $check = $data;
        unset($check['HASH']);
        $hash = $this->service->hasher->hashArray($check);
        if ($hash != $data['HASH']) {
            throw new InstantPaymentNotificationException('Invalid hash received');
        }
    }

    public function getData()
    {
        return $_POST;
    }


}