<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\RequestException;

abstract class Request implements RequestInterface
{
    /** @var string */
    protected $_url = '';

    /** @var string */
    protected $_method = self::METHOD_GET;

    /** @var array */
    protected $_query = [];

    public function __construct($url, $query = [])
    {
        $this->setUrl($url);
        $this->setQuery($query);
    }

    /** @inheritdoc */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /** @inheritdoc */
    public function setQuery(array $query)
    {
        $this->_query = $query;
        return $this;
    }

    /** @inheritdoc */
    public function setMethod($method)
    {
        switch ($method) {
            case self::METHOD_GET:
            case self::METHOD_POST:
                $this->_method = $method;
                break;
            default:
                throw new RequestException('Invalid method: ' . $method);
        }
        return $this;
    }

    /** @inheritdoc */
    public function parse($raw, $parser = null)
    {
        if (is_callable($parser)) {
            return $parser($raw);
        }
        return (array)simplexml_load_string($raw);
    }

    /** @inheritdoc */
    abstract public function fetch($parser = null);
}