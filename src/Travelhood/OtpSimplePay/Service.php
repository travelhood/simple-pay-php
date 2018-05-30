<?php

namespace Travelhood\OtpSimplePay;

/**
 * @property Config $config
 */
class Service extends Component
{
    /** @var Config */
    protected $_config;

    public function __construct(Config $config)
    {
        parent::__construct($this);
        $this->_config = $config;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        switch ($name) {
            case 'config':
                return $this->_config;
        }
        return parent::__get($name);
    }
}