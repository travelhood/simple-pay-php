<?php

namespace Travelhood\OtpSimplePay;

use Psr\Log\LoggerInterface;

/**
 * @property LoggerInterface $log
 * @property Service $service
 */
class Component
{
    /** @var LoggerInterface */
    protected $_logger;

    /** @var Service */
    protected $_service;

    /**
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->_service = $service;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        switch ($name) {
            case 'log':
                return $this->_service->log;
            case 'service':
                return $this->_service;
        }
        return null;
    }

}