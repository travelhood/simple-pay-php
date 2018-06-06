<?php

namespace Travelhood\OtpSimplePay\Page;

use Travelhood\OtpSimplePay\Page;
use Travelhood\OtpSimplePay\Service;

class Timeout extends Page
{
    public function getData()
    {
        return $_GET;
    }

    public function isTimeout()
    {
        return $this['redirect'] != 1;
    }

    public function isUserAction()
    {
        return !$this->isTimeout();
    }

    public function __toString()
    {
        if($this->isUserAction()) {
            return 'User has canceled the transaction';
        }
        return 'The transaction has timed out';
    }

}