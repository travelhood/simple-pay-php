<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\ProductException;

class Product implements ProductInterface
{
    const VALID_FIELDS = ['name', 'code', 'info', 'price', 'vat'];

    /** @var string */
    protected $_name = '';

    /** @var string */
    protected $_code = '';

    /** @var string */
    protected $_info = '';

    /** @var float */
    protected $_price = .0;

    /** @var float */
    protected $_vat = .0;

    /**
     * Product constructor.
     * @param array|string $name
     * @param string $code
     * @param string $info
     * @param float $price
     * @param float $vat
     */
    public function __construct($name='', $code='', $info='', $price=.0, $vat = .0)
    {
        if(is_array($name)) {
            $this->fromArray($name);
        }
        else {
            $this->_name = $name;
            $this->_code = $code;
            $this->_info = $info;
            $this->_price = $price;
            $this->_vat = $vat;
        }
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return in_array($offset, self::VALID_FIELDS);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if(!$this->offsetExists($offset)) {
            return null;
        }
        return $this->{'_'.$offset};
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws ProductException
     */
    public function offsetSet($offset, $value)
    {
        if(!$this->offsetExists($offset)) {
            throw new ProductException('Invalid field for Product: '.$offset);
        }
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        if(!$this->offsetExists($offset)) {
            return;
        }
        $this->{'_'.$offset} = null;
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

    /**
     * @param array $array
     * @return $this
     */
    public function fromArray(array $array)
    {
        foreach(self::VALID_FIELDS as $f) {
            $this->{'_'.$f} = $array[$f];
        }
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->_name,
            'code' => $this->_code,
            'info' => $this->_info,
            'price' => $this->_price,
            'vat' => $this->_vat,
        ];
    }

    public function __toString()
    {
        return $this->getName() . ' - ' . $this->getCode();
    }

    public function toHtml()
    {
        return '<strong>'.$this->_name.' ('.$this->_code.')</strong> '.$this->_info.'';
    }

}