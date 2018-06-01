<?php

namespace Travelhood\OtpSimplePay;

use ArrayAccess;

interface ProductInterface extends ArrayAccess, HtmlizeInterface
{
    function setName($name);

    function getName();

    function setCode($code);

    function getCode();

    function setInfo($info);

    function getInfo();

    function setPrice($price);

    function getPrice();

    function setVat($vat);

    function getVat();

    function fromArray(array $array);

    function toArray();

    function __toString();
}