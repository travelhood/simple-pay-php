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
    const VERSION = 'travelhood-v1.0.0';
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

    /**
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * @param string $currency
     * @return $this
     * @throws Exception\ConfigException
     */
    public function selectCurrency($currency)
    {
        if($this->_config->getCurrency() != $currency) {
            $this->_config->selectCurrency($currency);
            $this->log->info('Selected currency: ' . $currency);
        }
        return $this;
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

    /**
     * @return string
     */
    public function getUrlInstantDeliveryNotification()
    {
        return $this->_getUrlBase() . self::URL_INSTANT_DELIVERY_NOTIFICATION;
    }

    /**
     * @return string
     */
    public function getUrlInstantRefundNotification()
    {
        return $this->_getUrlBase() . self::URL_INSTANT_REFUND_NOTIFICATION;
    }

    /**
     * @return string
     */
    public function getUrlInstantOrderStatus()
    {
        return $this->_getUrlBase() . self::URL_INSTANT_ORDER_STATUS;
    }

    /**
     * @return string
     */
    public function getUrlTokens()
    {
        return $this->_getUrlBase() . self::URL_TOKENS;
    }

    /**
     * @param string $orderRef
     * @param string $orderDate
     * @return Order
     */
    public function createOrder($orderRef, $orderDate = null)
    {
        return new Order($this, $orderRef, $orderDate);
    }

    /**
     * @param $url
     * @param array $query
     * @return RequestInterface
     */
    public function createRequest($url, $query = [])
    {
        $this->log->debug('Creating request object', [
            'curl' => $this->config['curl'],
            'url' => $url,
            'query' => $query,
        ]);
        if ($this->config['curl']) {
            return new Request\Curl($url, $query);
        }
        return new Request\FileGetContents($url, $query);
    }

    /**
     * @return LiveUpdate
     */
    public function liveUpdate()
    {
        return new LiveUpdate($this);
    }

    /**
     * @return Page\Back
     */
    public function pageBack()
    {
        return new Page\Back($this);
    }

    /**
     * @return Page\Timeout
     */
    public function pageTimeout()
    {
        return new Page\Timeout($this);
    }

    /**
     * @return Page\PaymentNotification
     */
    public function pagePaymentNotification()
    {
        return new Page\PaymentNotification($this);
    }

    /**
     * @param string $orderRef
     * @param string $currency
     * @return Instant\OrderStatus
     * @throws Exception
     * @throws Exception\ConfigException
     * @throws Exception\InstantDeliveryNotificationException
     * @throws Exception\InstantOrderStatusException
     */
    public function instantOrderStatus($orderRef, $currency = null)
    {
        return new Instant\OrderStatus($this, $orderRef, $currency);
    }

    /**
     * @param string $simplePayRef
     * @param int $amount
     * @param string $currency
     * @return Instant\DeliveryNotification
     * @throws Exception
     * @throws Exception\ConfigException
     * @throws Exception\InstantDeliveryNotificationException
     * @throws Exception\RequestException
     */
    public function instantDeliveryNotification($simplePayRef, $amount, $currency = null)
    {
        return new Instant\DeliveryNotification($this, $simplePayRef, $amount, $currency);
    }

    /**
     * @param string $simplePayRef
     * @param int $orderAmount
     * @param int $refundAmount
     * @param string $currency
     * @return Instant\RefundNotification
     * @throws Exception
     * @throws Exception\ConfigException
     * @throws Exception\InstantDeliveryNotificationException
     */
    public function instantRefundNotification($simplePayRef, $orderAmount, $refundAmount, $currency = null)
    {
        return new Instant\RefundNotification($this, $simplePayRef, $orderAmount, $refundAmount, $currency);
    }
}