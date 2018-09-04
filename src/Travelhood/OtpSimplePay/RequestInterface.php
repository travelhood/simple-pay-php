<?php

namespace Travelhood\OtpSimplePay;

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
     * @return mixed
     */
    public function parse($raw, $parser = null);

    public function fetch($parser = null);
}