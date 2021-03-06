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

    public function __construct(Service $service)
    {
        parent::__construct($service);
        $this->log->debug($this->__toString());
    }

    public function __toString()
    {
        if ($this->isUserAction()) {
            return 'User has canceled the transaction';
        }
        return 'The transaction has timed out';
    }

    public function isUserAction()
    {
        return !$this->isTimeout();
    }

    public function isTimeout()
    {
        return $this['redirect'] != 1;
    }

}