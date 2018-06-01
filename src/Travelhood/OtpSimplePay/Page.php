<?php

namespace Travelhood\OtpSimplePay;

use ArrayAccess;
use Travelhood\OtpSimplePay\Exception\ConfigException;
use Travelhood\OtpSimplePay\Exception\PageException;
use Travelhood\OtpSimplePay\Exception\ControlMismatchException;

abstract class Page extends Component implements ArrayAccess
{
    /**
     * @return array
     */
    abstract public function getData();

    public function __construct(Service $service)
    {
        parent::__construct($service);
        $this->validate();
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->getData());
    }

    public function offsetGet($offset)
    {
        if(!$this->offsetExists($offset)) {
            return null;
        }
        return $this->getData()[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new PageException('Trying to set read-only variable');
    }

    public function offsetUnset($offset)
    {
        throw new PageException('Trying to set read-only variable');
    }

    public function toArray()
    {
        return $this->getData();
    }

    public function validate()
    {
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return false;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return null;
    }

    abstract public function __toString();
}