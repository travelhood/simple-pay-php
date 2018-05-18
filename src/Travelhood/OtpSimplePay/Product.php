<?php

namespace Travelhood\OtpSimplePay;

class Product
{
    /** @var string */
    protected $_name = '';

    /** @var string */
    protected $_code = '';

    /** @var string */
    protected $_info = '';

    /** @var float */
    protected $_price = 0.0;

    /** @var int */
    protected $_quantity = 1;

    /** @var float */
    protected $_vat = .0;

    /**
     * Product constructor.
     * @param string $name
     * @param string $code
     * @param float $price
     * @param float $vat
     */
    public function __construct($name, $code, $price, $vat = .0)
    {
        $this->_name = $name;
        $this->_code = $code;
        $this->_price = $price;
        $this->_vat = $vat;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $name
     * @return Product
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @param string $code
     * @return Product
     */
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->_info;
    }

    /**
     * @param string $info
     * @return Product
     */
    public function setInfo($info)
    {
        $this->_info = $info;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->_price;
    }

    /**
     * @param int $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->_price = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->_quantity;
    }

    /**
     * @param int $quantity
     * @return Product
     */
    public function setQuantity($quantity)
    {
        $this->_quantity = $quantity;
        return $this;
    }

    /**
     * @return int
     */
    public function getVat()
    {
        return $this->_vat;
    }

    /**
     * @param int $vat
     * @return Product
     */
    public function setVat($vat)
    {
        $this->_vat = $vat;
        return $this;
    }

    public function toArray()
    {
        return [
            'PNAME' => $this->_name,
            'PCODE' => $this->_code,
            'PINFO' => $this->_info,
            'PRICE' => $this->_price,
            'QTY' => $this->_quantity,
            'VAT' => $this->_vat,
        ];
    }
}