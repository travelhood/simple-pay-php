<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\OrderException;

interface OrderInterface
{
    /**
     * @return string
     */
    function getOrderRef();

    /**
     * @param string $orderRef
     * @return $this
     */
    function setOrderRef($orderRef);

    /**
     * @return string
     */
    function getLanguage();

    /**
     * @param string $language
     * @return $this
     */
    function setLanguage($language);

    /**
     * @return string
     */
    function getOrderDate();

    /**
     * @param string $orderDate
     * @return $this
     */
    function setOrderDate($orderDate);

    /**
     * @return int
     */
    function getOrderShipping();

    /**
     * @param int $orderShipping
     * @return $this
     */
    function setOrderShipping($orderShipping);

    /**
     * @return int
     */
    function getDiscount();

    /**
     * @param int $discount
     * @return $this
     */
    function setDiscount($discount);

    /**
     * @return string
     */
    function getPayMethod();

    /**
     * @param string $payMethod
     * @return $this
     */
    function setPayMethod($payMethod);

    /**
     * @return int
     */
    function getOrderTimeout();

    /**
     * @param int $orderTimeout
     * @return $this
     */
    function setOrderTimeout($orderTimeout);

    /**
     * @return string
     */
    function getUrlTimeout();

    /**
     * @param string $value
     * @return $this
     */
    function setUrlTimeout($value);

    /**
     * @return string
     */
    function getUrlBack();

    /**
     * @param string $value
     * @return $this
     */
    function setUrlBack($value);

    /**
     * @return string
     */
    function getBillFirstName();

    /**
     * @param string $billFirstName
     * @return $this
     */
    function setBillFirstName($billFirstName);

    /**
     * @return string
     */
    function getBillLastName();

    /**
     * @param string $billLastName
     * @return $this
     */
    function setBillLastName($billLastName);

    /**
     * @return string
     */
    function getBillEmail();

    /**
     * @param string $billEmail
     * @return $this
     */
    function setBillEmail($billEmail);

    /**
     * @return string
     */
    function getBillPhone();

    /**
     * @param string $billPhone
     * @return $this
     */
    function setBillPhone($billPhone);

    /**
     * @return $this
     */
    function getBillCompany();

    /**
     * @param string $billCompany
     * @return $this
     */
    function setBillCompany($billCompany);

    /**
     * @return $this
     */
    function getBillFiscalCode();

    /**
     * @param string $billFiscalCode
     * @return $this
     */
    function setBillFiscalCode($billFiscalCode);

    /**
     * @return string
     */
    function getBillCountryCode();

    /**
     * @param string $billCountryCode
     * @return $this
     */
    function setBillCountryCode($billCountryCode);

    /**
     * @return string
     */
    function getBillState();

    /**
     * @param string $billState
     * @return $this
     */
    function setBillState($billState);

    /**
     * @return string
     */
    function getBillCity();

    /**
     * @param string $billCity
     * @return $this
     */
    function setBillCity($billCity);

    /**
     * @return string
     */
    function getBillAddress();

    /**
     * @param string $billAddress
     * @return $this
     */
    function setBillAddress($billAddress);

    /**
     * @return string
     */
    function getBillAddress2();

    /**
     * @param string $billAddress2
     * @return $this
     */
    function setBillAddress2($billAddress2);

    /**
     * @return string
     */
    function getBillZipCode();

    /**
     * @param string $billZipCode
     * @return $this
     */
    function setBillZipCode($billZipCode);

    /**
     * @return string
     */
    function getDeliveryFirstName();

    /**
     * @param string $deliveryFirstName
     * @return $this
     */
    function setDeliveryFirstName($deliveryFirstName);

    /**
     * @return string
     */
    function getDeliveryLastName();
    /**
     * @param string $deliveryLastName
     * @return $this
     */
    function setDeliveryLastName($deliveryLastName);

    /**
     * @return string
     */
    function getDeliveryEmail();

    /**
     * @param string $deliveryEmail
     * @return $this
     */
    function setDeliveryEmail($deliveryEmail);

    /**
     * @return string
     */
    function getDeliveryPhone();

    /**
     * @param string $deliveryPhone
     * @return $this
     */
    function setDeliveryPhone($deliveryPhone);

    /**
     * @return string
     */
    function getDeliveryCountryCode();

    /**
     * @param string $deliveryCountryCode
     * @return $this
     */
    function setDeliveryCountryCode($deliveryCountryCode);

    /**
     * @return string
     */
    function getDeliveryState();

    /**
     * @param string $deliveryState
     * @return $this
     */
    function setDeliveryState($deliveryState);

    /**
     * @return string
     */
    function getDeliveryCity();

    /**
     * @param string $deliveryCity
     * @return $this
     */
    function setDeliveryCity($deliveryCity);

    /**
     * @return string
     */
    function getDeliveryAddress();

    /**
     * @param string $deliveryAddress
     * @return $this
     */
    function setDeliveryAddress($deliveryAddress);

    /**
     * @return string
     */
    function getDeliveryAddress2();

    /**
     * @param string $deliveryAddress2
     * @return $this
     */
    function setDeliveryAddress2($deliveryAddress2);

    /**
     * @return string
     */
    function getDeliveryZipCode();

    /**
     * @param string $deliveryZipCode
     * @return $this
     */
    function setDeliveryZipCode($deliveryZipCode);

    /**
     * Sets both billing and delivery first name
     * @param string $value
     * @return $this
     */
    function setFirstName($value);

    /**
     * Sets both billing and delivery last name
     * @param string $value
     * @return $this
     */
    function setLastName($value);

    /**
     * Sets both billing and delivery email
     * @param string $value
     * @return $this
     */
    function setEmail($value);

    /**
     * Sets both billing and delivery phone
     * @param string $value
     * @return $this
     */
    function setPhone($value);

    /**
     * Sets both billing and delivery address
     * @param string $value
     * @return $this
     */
    function setAddress($value);

    /**
     * Sets second line of both billing and delivery address
     * @param string $value
     * @return $this
     */
    function setAddress2($value);

    /**
     * Sets both billing and delivery zip code
     * @param string $value
     * @return $this
     */
    function setZipCode($value);

    /**
     * Sets both billing and delivery city
     * @param string $value
     * @return $this
     */
    function setCity($value);

    /**
     * Sets both billing and delivery state
     * @param string $value
     * @return $this
     */
    function setState($value);

    /**
     * Sets both billing and delivery country code
     * @param string $value
     * @return $this
     */
    function setCountryCode($value);

    /**
     * @return array
     */
    function toArray();

    /**
     * @return string
     */
    function __toString();

    /**
     * @return $this
     * @throws OrderException
     */
    function validate();
}