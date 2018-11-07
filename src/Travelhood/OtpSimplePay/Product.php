<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\ProductException;

class Product implements ProductInterface
{
    const VALID_FIELDS = [
        'name' => [
            'required' => true,
            'length' => 128,
        ],
        'code' => [
            'required' => true,
            'length' => 64,
        ],
        'info' => [
            'required' => false,
            'length' => 128,
        ],
        'price' => [
            'required' => true,
            'length' => 8,
        ],
        'vat' => [
            'required' => true,
            'length' => 2,
        ],
    ];

    /** @var string */
    protected $_name = null;

    /** @var string */
    protected $_code = null;

    /** @var string */
    protected $_info = null;

    /** @var float */
    protected $_price = null;

    /** @var float */
    protected $_vat = null;


    /**
     * Product constructor.
     * @param array|string $name
     * @param string $code
     * @param string $info
     * @param float $price
     * @param float $vat
     */
    public function __construct(
        $name = null,
        /** @noinspection PhpUnusedParameterInspection */
        $code = null,
        /** @noinspection PhpUnusedParameterInspection */
        $info = null,
        /** @noinspection PhpUnusedParameterInspection */
        $price = null,
        /** @noinspection PhpUnusedParameterInspection */
        $vat = null
    )
    {
        if (is_array($name)) {
            $this->fromArray($name);
        } else {
            foreach (self::VALID_FIELDS as $f => $d) {
                if($$f !== null) {
                    $this->{'_' . $f} = $$f;
                }
            }
        }
    }

    /**
     * @return $this
     * @throws ProductException
     */
    public function validate()
    {
        foreach (self::VALID_FIELDS as $f => $d) {
            if ($this->{'_' . $f} === null) {
                if ($d['required']) {
                    throw new ProductException('Missing mandatory field: ' . $f);
                }
            } else {
                $this->{'_' . $f} = substr($this->{'_' . $f}, 0, min(strlen($this->{'_' . $f}), $d['length']));
            }
        }
        return $this;
    }

    /**
     * @param array $array
     * @return $this
     */
    public function fromArray(array $array)
    {
        foreach (self::VALID_FIELDS as $f => $d) {
            $this->{'_' . $f} = $array[$f];
        }
        return $this;
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }
        return $this->{'_' . $offset};
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, self::VALID_FIELDS);
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws ProductException
     */
    public function offsetSet($offset, $value)
    {
        if (!$this->offsetExists($offset)) {
            throw new ProductException('Invalid field for Product: ' . $offset);
        }
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        if (!$this->offsetExists($offset)) {
            return;
        }
        $this->{'_' . $offset} = null;
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
     * @return array
     * @throws ProductException
     */
    public function toArray()
    {
        $this->validate();
        return [
            'name' => $this->_name,
            'code' => $this->_code,
            'info' => $this->_info,
            'price' => $this->_price,
            'vat' => $this->_vat,
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() . ' - ' . $this->getCode();
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

}