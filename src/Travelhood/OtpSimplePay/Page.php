<?php

namespace Travelhood\OtpSimplePay;

use ArrayAccess;
use Travelhood\OtpSimplePay\Exception\PageException;

abstract class Page extends Component implements ArrayAccess
{
    public function __construct(Service $service)
    {
        parent::__construct($service);
        $this->validate();
    }

    public function validate()
    {
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }
        return $this->getData()[$offset];
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->getData());
    }

    /**
     * @return array
     */
    abstract public function getData();

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