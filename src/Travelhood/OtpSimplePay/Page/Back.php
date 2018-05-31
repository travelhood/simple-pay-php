<?php

namespace Travelhood\OtpSimplePay\Page;

use Travelhood\OtpSimplePay\Page;

class Back extends Page
{
    public function getMessage()
    {
        if($this->hasError()) {
            return 'User has cancelled the transaction';
        }
        else {
            return 'Successful transaction!';
        }
    }
}