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

    /**
     * Request constructor.
     * @param string $url
     * @param array $query
     */
    public function __construct($url, $query=[])
    {
        $this->setUrl($url);
        $this->setQuery($query);
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     * @throws RequestException
     */
    public function setMethod($method)
    {
        switch ($method) {
            case self::METHOD_GET:
            case self::METHOD_POST:
                $this->_method = $method;
                break;
            default:
                throw new RequestException('Invalid method: '.$method);
        }
        return $this;
    }

    /**
     * @param array $query
     * @return $this
     */
    public function setQuery(array $query)
    {
        $this->_query = $query;
        return $this;
    }

    /**
     * @param string $raw
     * @return array
     */
    public function parse($raw)
    {
        return (array) simplexml_load_string($raw);
    }

    /**
     * @return mixed
     */
    abstract public function fetch();
}