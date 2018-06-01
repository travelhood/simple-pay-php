<?php

namespace Travelhood\OtpSimplePay\Page;

use Travelhood\OtpSimplePay\Page;

class Timeout extends Page
{

    public function getData()
    {
        return $_GET;
    }

    public function __toString()
    {
        if($this['redirect'] == 1) {
            return 'User has canceled the transaction';
        }
        return 'The transaction has timed out';
    }

}