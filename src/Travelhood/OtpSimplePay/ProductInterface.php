<?php

namespace Travelhood\OtpSimplePay;

use ArrayAccess;
use Travelhood\OtpSimplePay\Exception\ProductException;

interface ProductInterface extends ArrayAccess
{
    /**
     * @return $this
     * @throws ProductException
     */
    function validate();

    /**
     * Sets the name of the product
     * @param string $name
     * @return $this
     */
    function setName($name);

    /**
     * Gets the name of the product
     * @return string
     */
    function getName();

    /**
     * Sets the code (SKU) of the product
     * @param string $code
     * @return $this
     */
    function setCode($code);

    /**
     * Gets the code (SKU) of the product
     * @return string
     */
    function getCode();

    /**
     * Sets additional text info for the product
     * @param string $info
     * @return $this
     */
    function setInfo($info);

    /**
     * Gets additional text info about the product
     * @return string
     */
    function getInfo();

    /**
     * Sets the price of the product
     * @param float $price
     * @return $this
     */
    function setPrice($price);

    /**
     * Gets the price of the product
     * @return float
     */
    function getPrice();

    /**
     * Sets Various Added Tax in percent for the product
     * @param float $vat
     * @return $this
     */
    function setVat($vat);

    /**
     * Sets Various Added Tax in percent for the product
     * @return float
     */
    function getVat();

    /**
     * Copies values from array
     * @param array $array
     * @return $this
     */
    function fromArray(array $array);

    /**
     * Returns array representation
     * @return array
     */
    function toArray();

    /**
     * Generates string representation
     * @return string
     */
    function __toString();
}