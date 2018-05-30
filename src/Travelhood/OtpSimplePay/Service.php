<?php

namespace Travelhood\OtpSimplePay;

/**
 * @property Config $config
 */
class Service extends Component
{
    const URL_LIVE = "https://secure.simplepay.hu/payment/";
    const URL_SANDBOX = "https://sandbox.simplepay.hu/payment/";
    const URL_LIVE_UPDATE = "order/lu.php";
    const URL_INSTANT_DELIVERY_NOTIFICATION = "order/idn.php";
    const URL_INSTANT_REFUND_NOTIFICATION = "order/irn.php";
    const URL_INSTANT_ORDER_STATUS = "order/ios.php";
    const URL_TOKENS = "order/tokens/";

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

    public function createLiveUpdate()
    {
        return new LiveUpdate($this);
    }
}