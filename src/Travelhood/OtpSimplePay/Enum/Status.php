<?php

namespace Travelhood\OtpSimplePay\Enum;

final class Status
{
    // Initiated transaction on payment page (LiveUpdate)
    const CARD_UNAUTHORIZED = 'CARD_NOTAUTHORIZED';

    // Timeout on payment page (LiveUpdate, Timeout)
    const TIMEOUT = 'TIMEOUT';

    // Cancelled payment on payment page (LiveUpdate, Timeout)
    const CANCELLED = 'CANCELLED';

    // Waiting for payment in case of credit card payment or wire transfer (LiveUpdate, BackRef)
    const WAITING_PAYMENT = 'WAITING_PAYMENT';

    // Successful authorization (BackRef)
    const PAYMENT_AUTHORIZED = 'PAYMENT_AUTHORIZED';

    // Suspected fraud (BackRef)
    const FRAUD = 'FRAUD';

    // Reversed reserved amount (two-step payment) (IRN)
    const REVERSED = 'REVERSED';

    // Successful, completed transaction (IPN, IDN)
    const COMPLETE = 'COMPLETE';

    // Refunded (partial or total) (URN)
    const REFUND = 'REFUND';

    /**
     * Instance not allowed
     */
    private function __construct()
    {
    }
}