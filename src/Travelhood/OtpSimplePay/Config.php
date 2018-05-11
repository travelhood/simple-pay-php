<?php

namespace Travelhood\OtpSimplePay;

use ArrayObject;
use ArrayIterator;

class Config extends ArrayObject
{
    const CURRENCIES = ['USD', 'EUR', 'HUF'];
    const KEYS_REQUIRED = ['MERCHANT', 'SECRET_KEY'];

    public function __construct($config=[])
    {
        parent::__construct($config, ArrayObject::ARRAY_AS_PROPS, ArrayIterator::class);
    }

    public function setCurrency($currency)
    {

    }
}