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
        $this->log->info('Confirmed IPN', [
            'request'=>$this->getData(),
            'date'=>$date,
            'hash'=>$hash
        ]);
        return '<EPAYMENT>' . $date . '|' . $hash . '</EPAYMENT>';
    }

    public function validate()
    {
        $this->log->debug('Validating IPN', $this->getData());
        $data = $this->getData();
        if (!array_key_exists('CURRENCY', $data)) {
            $this->log->critical('Invalid request received');
            throw new InstantPaymentNotificationException('Invalid request received');
        }
        $this->service->selectCurrency($data['CURRENCY']);
        $check = $data;
        unset($check['HASH']);
        $hash = $this->service->hasher->hashArray($check);
        if ($hash != $data['HASH']) {
            $this->log->critical('Invalid hash received', $this->getData());
            throw new InstantPaymentNotificationException('Invalid hash received for order '.$this->getOrderRef());
        }
        $this->log->info('Validated IPN');
    }

    public function getData()
    {
        return $_POST;
    }

    #region getters

    /* Sample request data:
        "SALEDATE": "2018-06-19 12:21:29",
        "REFNO": "...",
        "REFNOEXT": "...",
        "ORDERNO": "",
        "ORDERSTATUS": "PAYMENT_AUTHORIZED",
        "PAYMETHOD": "CCVISAMC",
        "FIRSTNAME": "...",
        "LASTNAME": "...",
        "IDENTITY_NO": "",
        "IDENTITY_ISSUER": "",
        "IDENTITY_CNP": "",
        "REGISTRATIONNUMBER": "",
        "CBANKNAME": "",
        "CBANKACCOUNT": "",
        "ADDRESS1": "...",
        "CITY": "...",
        "STATE": "...",
        "ZIPCODE": "...",
        "COUNTRY": "...",
        "PHONE": "...",
        "CUSTOMEREMAIL": "...",
        "FIRSTNAME_D": "...",
        "LASTNAME_D": "...",
        "COMPANY_D": "",
        "ADDRESS1_D": "...",
        "CITY_D": "...",
        "ZIPCODE_D": "...",
        "COUNTRY_D": "...",
        "PHONE_D": "...",
        "IPADDRESS": "...",
        "CURRENCY": "...",
        "IPN_PID": [
            "895177222",
            "670493899"
        ],
        "IPN_PNAME": [
            "Product 1",
            "Product 2"
        ],
        "IPN_PCODE": [
            "sku123",
            "sku456"
        ],
        "IPN_INFO": [
            "Some nice product",
            "Another awesome product"
        ],
        "IPN_QTY": [
            "1",
            "5"
        ],
        "IPN_PRICE": [
            "5000",
            "10000"
        ],
        "IPN_VAT": [
            "0.27",
            "0.14"
        ],
        "IPN_VER": [
            "",
            ""
        ],
        "IPN_DISCOUNT": [
            "",
            ""
        ],
        "IPN_PROMONAME": [
            "",
            ""
        ],
        "IPN_DELIVEREDCODES": [
            "",
            ""
        ],
        "IPN_TOTAL": [
            "5013.5",
            "50070.0"
        ],
        "IPN_TOTALGENERAL": "55084.0",
        "IPN_SHIPPING": "0.0",
        "IPN_COMMISSION": "0.00",
        "IPN_DATE": "20180620111958",
        "HASH": "..."
     */

    public function getDate()
    {
        return $this['SALEDATE'];
    }

    public function getOrderRef()
    {
        return $this['REFNOEXT'];
    }

    public function getSimplePayRef()
    {
        return $this['REFNO'];
    }

    public function getOrderStatus()
    {
        return $this['ORDERSTATUS'];
    }

    #endregion


}