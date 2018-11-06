<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\RequestException;

interface RequestInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * @param string $method
     * @return $this
     * @throws RequestException
     */
    public function setMethod($method);

    /**
     * @param array $query
     * @return $this
     */
    public function setQuery(array $query);

    /**
     * @param string $raw
     * @param callable $parser
     * @return array
     * @throws RequestException
     */
    public function parse($raw, $parser = null);

    /**
     * @param string $parser
     * @return mixed
     * @throws RequestException
     */
    public function fetch($parser = null);
}