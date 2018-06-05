<?php

namespace Travelhood\OtpSimplePay;

interface RequestInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    public function setUrl($url);

    public function setMethod($method);

    public function setQuery(array $query);

    public function parse($raw, $parser=null);

    public function fetch($parser=null);
}