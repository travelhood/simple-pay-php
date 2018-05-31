<?php

namespace Travelhood\OtpSimplePay\Page;

use Travelhood\OtpSimplePay\Page;

class Timeout extends Page
{
    public function getMessage()
    {
        return 'The transaction has timed out';
    }
}