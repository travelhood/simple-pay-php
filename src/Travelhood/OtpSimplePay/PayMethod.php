<?php

namespace Travelhood\OtpSimplePay;

final class PayMethod
{
    const CARD = 'CCVISAMC';
    const WIRE = 'WIRE';

    /**
     * Instance not allowed
     */
    private function __construct() { }
}