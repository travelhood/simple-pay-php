<?php

namespace Travelhood\OtpSimplePay;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Travelhood\OtpSimplePay\Logger\FileLogger;

/**
 * @property Config $config
 * @property Hasher $hasher
 */
class Service extends Component implements LoggerAwareInterface
{
    const VERSION = 'travelhood-v0.1.12';
    const URL_LIVE = "https://secure.simplepay.hu/payment/";
    const URL_SANDBOX = "https://sandbox.simplepay.hu/payment/";
    const URL_LIVE_UPDATE = "order/lu.php";
    const URL_INSTANT_DELIVERY_NOTIFICATION = "order/idn.php";
    const URL_INSTANT_REFUND_NOTIFICATION = "order/irn.php";
    const URL_INSTANT_ORDER_STATUS = "order/ios.php";
    const URL_TOKENS = "order/tokens/";

    /** @var Config */
    protected $_config;

    /** @var Hasher */
    protected $_hasher;

    public function __construct(Config $config)
    {
        parent::__construct($this);
        $this->_config = $config;
        $this->_hasher = new Hasher($this);
        if (is_array($config['log'])) {
            $this->_logger = new FileLogger($config['log']['path'], $config['log']['level'], $config['log']['prefix']);
        } else {
            $this->_logger = new NullLogger;
        }
        $this->log->debug('Service instance created');
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;
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
            case 'hasher':
                return $this->_hasher;
        }
        return parent::__get($name);
    }

    public function getUrlLiveUpdate()
    {
        return $this->_getUrlBase() . self::URL_LIVE_UPDATE;
    }

    protected function _getUrlBase()
    {
        if ($this->config['live']) {
            return self::URL_LIVE;
        }
        return self::URL_SANDBOX;
    }

    public function getUrlInstantDeliveryNotification()
    {
        return $this->_getUrlBase() . self::URL_INSTANT_DELIVERY_NOTIFICATION;
    }

    public function getUrlInstantRefundNotification()
    {
        return $this->_getUrlBase() . self::URL_INSTANT_REFUND_NOTIFICATION;
    }

    public function getUrlInstantOrderStatus()
    {
        return $this->_getUrlBase() . self::URL_INSTANT_ORDER_STATUS;
    }

    public function getUrlTokens()
    {
        return $this->_getUrlBase() . self::URL_TOKENS;
    }

    public function createOrder($orderRef, $orderDate = null)
    {
        return new Order($this, $orderRef, $orderDate);
    }

    public function createRequest($url, $query = [])
    {
        if ($this->config['curl']) {
            return new Request\Curl($url, $query);
        }
        return new Request\FileGetContents($url, $query);
    }

    public function liveUpdate()
    {
        return new LiveUpdate($this);
    }

    public function pageBack()
    {
        return new Page\Back($this);
    }

    public function pageTimeout()
    {
        return new Page\Timeout($this);
    }

    public function pagePaymentNotification()
    {
        return new Page\PaymentNotification($this);
    }

    public function instantOrderStatus($orderRef, $currency = null)
    {
        return new Instant\OrderStatus($this, $orderRef, $currency);
    }

    public function instantDeliveryNotification($simplePayRef, $amount, $currency = null)
    {
        return new Instant\DeliveryNotification($this, $simplePayRef, $amount, $currency);
    }

    public function instantRefundNotification($simplePayRef, $orderAmount, $refundAmount, $currency = null)
    {
        return new Instant\RefundNotification($this, $simplePayRef, $orderAmount, $refundAmount, $currency);
    }
}