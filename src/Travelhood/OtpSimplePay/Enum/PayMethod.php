<?php

namespace Travelhood\OtpSimplePay\Enum;

final class PayMethod
{
    // Card: Visa / MasterCard
    const CARD = 'CCVISAMC';

    // Wire transfer
    const WIRE = 'WIRE';

    /**
     * Instance not allowed
     */
    private function __construct() { }
}