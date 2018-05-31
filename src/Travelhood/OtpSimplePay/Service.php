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

    protected function _getUrlBase()
    {
        if($this->config['live']) {
            return self::URL_LIVE;
        }
        return self::URL_SANDBOX;
    }

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

    public function getUrlLiveUpdate()
    {
        return $this->_getUrlBase().self::URL_LIVE_UPDATE;
    }

    public function getUrlInstantDeliveryNotification()
    {
        return $this->_getUrlBase().self::URL_INSTANT_DELIVERY_NOTIFICATION;
    }

    public function getUrlInstantRefundNotification()
    {
        return $this->_getUrlBase().self::URL_INSTANT_REFUND_NOTIFICATION;
    }

    public function getUrlInstantOrderStatus()
    {
        return $this->_getUrlBase().self::URL_INSTANT_ORDER_STATUS;
    }

    public function getUrlTokens()
    {
        return $this->_getUrlBase().self::URL_TOKENS;
    }

    public function pageBack()
    {
        return new Page\Back($this);
    }

    public function pageTimeout()
    {
        return new Page\Timeout($this);
    }

    public function createOrder($orderRef, $orderDate=null)
    {
        return new Order($this, $orderRef, $orderDate);
    }

    public function createLiveUpdate()
    {
        return new LiveUpdate($this);
    }
}