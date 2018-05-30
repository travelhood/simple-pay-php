<?php

namespace Travelhood\OtpSimplePay;

/**
 * @property ProductCollection $products
 */
class LiveUpdate extends Component
{
    private $_products;

    public function __construct(Service $service)
    {
        parent::__construct($service);
        $this->_products = new ProductCollection;
    }

    public function __get($name)
    {
        switch($name) {
            case 'products':
                return $this->_products;
        }
        return parent::__get($name);
    }

    public function createForm()
    {

    }
}