<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\OrderException;

/**
 * @property ProductCollectionInterface|ProductInterface[] $products
 */
class Order extends Component implements OrderInterface
{
    /** @var array Include these fields in this order when creating the hash */
    const HASH_FIELDS = [
        'MERCHANT',
        'ORDER_REF',
        'ORDER_DATE',
        'ORDER_PNAME',
        'ORDER_PCODE',
        'ORDER_PINFO',
        'ORDER_PRICE',
        'ORDER_QTY',
        'ORDER_VAT',
        'ORDER_SHIPPING',
        'PRICES_CURRENCY',
        'DISCOUNT',
        'PAY_METHOD',
    ];

    const VALIDATE_FIELDS = [
        'orderRef' => [
            'required' => true,
            'length' => 256,
        ],
        'language' => [
            'required' => false,
            'length' => 2,
        ],
        'orderShipping' => [
            'required' => false,
            'length' => 8,
        ],
        'discount' => [
            'required' => false,
            'length' => 8,
        ],
        'payMethod' => [
            'required' => false,
            'length' => 8,
        ],
        'urlTimeout' => [
            'required' => true,
            'length' => 256,
        ],
        'urlBack' => [
            'required' => true,
            'length' => 256,
        ],
        'billFirstName' => [
            'required' => false,
            'length' => 64,
        ],
        'billLastName' => [
            'required' => false,
            'length' => 64,
        ],
        'billEmail' => [
            'required' => true,
            'length' => 64,
        ],
        'billPhone' => [
            'required' => false,
            'length' => 32,
        ],
        'billCompany' => [
            'required' => false,
            'length' => 128,
        ],
        'billFiscalCode' => [
            'required' => false,
            'length' => 64,
        ],
        'billCountryCode' => [
            'required' => false,
            'length' => 2,
        ],
        'billState' => [
            'required' => false,
            'length' => 64,
        ],
        'billCity' => [
            'required' => false,
            'length' => 128,
        ],
        'billAddress' => [
            'required' => false,
            'length' => 128,
        ],
        'billAddress2' => [
            'required' => false,
            'length' => 128,
        ],
        'billZipCode' => [
            'required' => false,
            'length' => 32,
        ],
        'deliveryFirstName' => [
            'required' => false,
            'length' => 64,
        ],
        'deliveryLastName' => [
            'required' => false,
            'length' => 64,
        ],
        'deliveryEmail' => [
            'required' => false,
            'length' => 64,
        ],
        'deliveryPhone' => [
            'required' => false,
            'length' => 32,
        ],
        'deliveryCountryCode' => [
            'required' => false,
            'length' => 2,
        ],
        'deliveryState' => [
            'required' => false,
            'length' => 64,
        ],
        'deliveryCity' => [
            'required' => false,
            'length' => 128,
        ],
        'deliveryAddress' => [
            'required' => false,
            'length' => 128,
        ],
        'deliveryAddress2' => [
            'required' => false,
            'length' => 128,
        ],
        'deliveryZipCode' => [
            'required' => false,
            'length' => 32,
        ],
    ];


    /**
     * Set this PAY_METHOD for all subsequent orders as default
     * @var string
     */
    public static $DefaultPayMethod = Enum\PayMethod::CARD;


    /**
     * Set this LANGUAGE for all subsequent orders as default
     * @var string
     */
    public static $DefaultLanguage = Enum\Language::EN;


    /** @var string */
    protected $_urlBack = '';
    /** @var string */
    protected $_urlTimeout = '';
    /** @var string */
    protected $_orderRef = '';
    /** @var string */
    protected $_orderDate = '';
    /** @var int */
    protected $_orderTimeout = 60;
    /** @var int */
    protected $_orderShipping = 0;
    /** @var int */
    protected $_discount = 0;
    /** @var string */
    protected $_payMethod = '';
    /** @var string */
    protected $_language = '';
    /** @var string */
    protected $_billFirstName = '';
    /** @var string */
    protected $_billLastName = '';
    /** @var string */
    protected $_billCompany = '';
    /** @var string */
    protected $_billFiscalCode = '';
    /** @var string */
    protected $_billEmail = '';
    /** @var string */
    protected $_billPhone = '';
    /** @var string */
    protected $_billAddress = '';
    /** @var string */
    protected $_billAddress2 = '';
    /** @var string */
    protected $_billZipCode = '';
    /** @var string */
    protected $_billCity = '';
    /** @var string */
    protected $_billState = '';
    /** @var string */
    protected $_billCountryCode = '';
    /** @var string */
    protected $_deliveryFirstName = '';
    /** @var string */
    protected $_deliveryLastName = '';
    /** @var string */
    protected $_deliveryPhone = '';
    /** @var string */
    protected $_deliveryEmail = '';
    /** @var string */
    protected $_deliveryAddress = '';
    /** @var string */
    protected $_deliveryAddress2 = '';
    /** @var string */
    protected $_deliveryZipCode = '';
    /** @var string */
    protected $_deliveryCity = '';
    /** @var string */
    protected $_deliveryState = '';
    /** @var string */
    protected $_deliveryCountryCode = '';


    /** @var ProductCollectionInterface|ProductInterface[] */
    private $_products;

    /**
     * @param Service $service
     * @param string $orderRef
     * @param string $orderDate
     */
    public function __construct(Service $service, $orderRef, $orderDate = null)
    {
        parent::__construct($service);
        $this->_products = new ProductCollection;
        $this->setOrderRef($orderRef);
        if (!$orderDate) {
            $orderDate = date('Y-m-d H:i:s');
        }
        $this->setOrderDate($orderDate);
        $this->setOrderTimeout($this->service->config['timeout']);
        $this->setPayMethod(self::$DefaultPayMethod);
        $this->setLanguage(self::$DefaultLanguage);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'products':
                return $this->_products;
        }
        return parent::__get($name);
    }

    /**
     * @return string
     * @throws OrderException
     */
    public function __toString()
    {
        $s = $this->products . '' . PHP_EOL;
        foreach ($this->toArray() as $k => $v) {
            if (is_array($v)) {
                continue;
            }
            $s .= $k . ': ' . $v . PHP_EOL;
        }
        return $s;
    }

    /**
     * @return array
     * @throws OrderException
     */
    public function toArray()
    {
        $this->validate();
        $pname = [];
        $pcode = [];
        $pinfo = [];
        $pprice = [];
        $pqty = [];
        $pvat = [];
        foreach ($this->products as $product) {
            $pname[] = $product->getName();
            $pcode[] = $product->getCode();
            $pinfo[] = $product->getInfo();
            $pprice[] = $product->getPrice();
            $pqty[] = $this->products->countProduct($product);
            $pvat[] = $product->getVat();
        }
        $query = '?order_ref=' . urlencode($this->getOrderRef()) . '&order_currency=' . urlencode($this->service->config->getCurrency());
        $array = [
            'MERCHANT' => $this->service->config['merchant_id'],
            'ORDER_REF' => $this->getOrderRef(),
            'LANGUAGE' => $this->getLanguage(),
            'ORDER_DATE' => $this->getOrderDate(),
            'PRICES_CURRENCY' => $this->service->config->getCurrency(),
            'ORDER_SHIPPING' => $this->getOrderShipping(),
            'DISCOUNT' => $this->getDiscount(),
            'PAY_METHOD' => $this->getPayMethod(),
            'ORDER_TIMEOUT' => $this->getOrderTimeout(),
            'TIMEOUT_URL' => $this->getUrlTimeout() . $query,
            'BACK_REF' => $this->getUrlBack() . $query,
            'ORDER_PNAME' => $pname,
            'ORDER_PCODE' => $pcode,
            'ORDER_PINFO' => $pinfo,
            'ORDER_PRICE' => $pprice,
            'ORDER_QTY' => $pqty,
            'ORDER_VAT' => $pvat,
            'BILL_FNAME' => $this->getBillFirstName(),
            'BILL_LNAME' => $this->getBillLastName(),
            'BILL_EMAIL' => $this->getBillEmail(),
            'BILL_PHONE' => $this->getBillPhone(),
            'BILL_COMPANY' => $this->getBillCompany(),
            'BILL_FISCALCODE' => $this->getBillFiscalCode(),
            'BILL_COUNTRYCODE' => strtoupper($this->getBillCountryCode()),
            'BILL_STATE' => $this->getBillState(),
            'BILL_CITY' => $this->getBillCity(),
            'BILL_ADDRESS' => $this->getBillAddress(),
            'BILL_ADDRESS2' => $this->getBillAddress2(),
            'BILL_ZIPCODE' => $this->getBillZipCode(),
            'DELIVERY_FNAME' => $this->getDeliveryFirstName(),
            'DELIVERY_LNAME' => $this->getDeliveryLastName(),
            'DELIVERY_EMAIL' => $this->getDeliveryEmail(),
            'DELIVERY_PHONE' => $this->getDeliveryPhone(),
            'DELIVERY_COUNTRYCODE' => strtoupper($this->getDeliveryCountryCode()),
            'DELIVERY_STATE' => $this->getDeliveryState(),
            'DELIVERY_CITY' => $this->getDeliveryCity(),
            'DELIVERY_ADDRESS' => $this->getDeliveryAddress(),
            'DELIVERY_ADDRESS2' => $this->getDeliveryAddress2(),
            'DELIVERY_ZIPCODE' => $this->getDeliveryZipCode(),
            'SDK_VERSION' => Service::VERSION,
        ];
        $serial = '';
        foreach (self::HASH_FIELDS as $field) {
            if (is_array($array[$field])) {
                foreach ($array[$field] as $v) {
                    $serial .= strlen($v) . $v;
                }
            } else {
                $v = $array[$field];
                $serial .= strlen($v) . $v;
            }
        }
        $hash = $this->service->hasher->hashString($serial);
        $array['ORDER_HASH'] = $hash;
        return $array;
    }

    /**
     * @throws Exception\ProductException
     * @throws OrderException
     * @return $this|OrderInterface
     */
    public function validate()
    {
        foreach(self::VALIDATE_FIELDS as $field => $validator) {
            $fl = strlen($this->{'_'.$field});
            if($validator['required'] && $fl < 1) {
                throw new OrderException('Missing field: ' . $field);
            }
            if($fl > $validator['length']) {
                $this->{'_'.$field} = substr($this->{'_'.$field}, 0, $validator['length']);
            }
        }
        if ($this->products->count() < 1) {
            throw new OrderException('No product specified');
        }
        foreach($this->products as $product) {
            $product->validate();
        }
        return $this;
    }

    #region getters/setters

    /**
     * @return string
     */
    public function getOrderRef()
    {
        return $this->_orderRef;
    }

    /**
     * @param string $orderRef
     * @return $this
     */
    public function setOrderRef($orderRef)
    {
        $this->_orderRef = $orderRef;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderDate()
    {
        return $this->_orderDate;
    }

    /**
     * @param string $orderDate
     * @return $this
     */
    public function setOrderDate($orderDate)
    {
        $this->_orderDate = $orderDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderShipping()
    {
        return $this->_orderShipping;
    }

    /**
     * @param int $orderShipping
     * @return $this
     */
    public function setOrderShipping($orderShipping)
    {
        $this->_orderShipping = $orderShipping;
        return $this;
    }

    /**
     * @return int
     */
    public function getDiscount()
    {
        return $this->_discount;
    }

    /**
     * @param int $discount
     * @return $this
     */
    public function setDiscount($discount)
    {
        $this->_discount = $discount;
        return $this;
    }

    /**
     * @return string
     */
    public function getPayMethod()
    {
        return $this->_payMethod;
    }

    /**
     * @param string $payMethod
     * @return $this
     */
    public function setPayMethod($payMethod)
    {
        $this->_payMethod = $payMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * @param string $language
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->_language = $language;
        return $this;
    }

    /**
     * @return int
     */
    function getOrderTimeout()
    {
        return $this->_orderTimeout;
    }

    /**
     * @param int $orderTimeout
     * @return $this
     */
    function setOrderTimeout($orderTimeout)
    {
        $this->_orderTimeout = $orderTimeout;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrlTimeout()
    {
        if (!$this->_urlTimeout) {
            return $this->service->config['url']['timeout'];
        }
        return $this->_urlTimeout;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUrlTimeout($value)
    {
        $this->_urlTimeout = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrlBack()
    {
        if (!$this->_urlBack) {
            return $this->service->config['url']['back'];
        }
        return $this->_urlBack;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUrlBack($value)
    {
        $this->_urlBack = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillFirstName()
    {
        return $this->_billFirstName;
    }

    /**
     * @param string $billFirstName
     * @return $this
     */
    public function setBillFirstName($billFirstName)
    {
        $this->_billFirstName = $billFirstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillLastName()
    {
        return $this->_billLastName;
    }

    /**
     * @param string $billLastName
     * @return $this
     */
    public function setBillLastName($billLastName)
    {
        $this->_billLastName = $billLastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillEmail()
    {
        return $this->_billEmail;
    }

    /**
     * @param string $billEmail
     * @return $this
     */
    public function setBillEmail($billEmail)
    {
        $this->_billEmail = $billEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillPhone()
    {
        return $this->_billPhone;
    }

    /**
     * @param string $billPhone
     * @return $this
     */
    public function setBillPhone($billPhone)
    {
        $this->_billPhone = $billPhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillAddress()
    {
        return $this->_billAddress;
    }

    /**
     * @param string $billAddress
     * @return $this
     */
    public function setBillAddress($billAddress)
    {
        $this->_billAddress = $billAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillAddress2()
    {
        return $this->_billAddress2;
    }

    /**
     * @param string $billAddress2
     * @return $this
     */
    public function setBillAddress2($billAddress2)
    {
        $this->_billAddress2 = $billAddress2;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillZipCode()
    {
        return $this->_billZipCode;
    }

    /**
     * @param string $billZipCode
     * @return $this
     */
    public function setBillZipCode($billZipCode)
    {
        $this->_billZipCode = $billZipCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillCity()
    {
        return $this->_billCity;
    }

    /**
     * @param string $billCity
     * @return $this
     */
    public function setBillCity($billCity)
    {
        $this->_billCity = $billCity;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillState()
    {
        return $this->_billState;
    }

    /**
     * @param string $billState
     * @return $this
     */
    public function setBillState($billState)
    {
        $this->_billState = $billState;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillCompany()
    {
        return $this->_billCompany;
    }

    /**
     * @param $billCompany
     * @return $this
     */
    public function setBillCompany($billCompany)
    {
        $this->_billCompany = $billCompany;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillFiscalCode()
    {
        return $this->_billFiscalCode;
    }

    /**
     * @param $billFiscalCode
     * @return $this
     */
    public function setBillFiscalCode($billFiscalCode)
    {
        $this->_billFiscalCode = $billFiscalCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillCountryCode()
    {
        return $this->_billCountryCode;
    }

    /**
     * @param string $billCountryCode
     * @return $this
     */
    public function setBillCountryCode($billCountryCode)
    {
        $this->_billCountryCode = $billCountryCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryFirstName()
    {
        return $this->_deliveryFirstName;
    }

    /**
     * @param string $deliveryFirstName
     * @return $this
     */
    public function setDeliveryFirstName($deliveryFirstName)
    {
        $this->_deliveryFirstName = $deliveryFirstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryLastName()
    {
        return $this->_deliveryLastName;
    }

    /**
     * @param string $deliveryLastName
     * @return $this
     */
    public function setDeliveryLastName($deliveryLastName)
    {
        $this->_deliveryLastName = $deliveryLastName;
        return $this;
    }

    /**
     * @return string
     */
    function getDeliveryEmail()
    {
        return $this->_deliveryEmail;
    }

    /**
     * @param string $deliveryEmail
     * @return $this
     */
    function setDeliveryEmail($deliveryEmail)
    {
        $this->_deliveryEmail = $deliveryEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryPhone()
    {
        return $this->_deliveryPhone;
    }

    /**
     * @param string $deliveryPhone
     * @return $this
     */
    public function setDeliveryPhone($deliveryPhone)
    {
        $this->_deliveryPhone = $deliveryPhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryAddress()
    {
        return $this->_deliveryAddress;
    }

    /**
     * @param string $deliveryAddress
     * @return $this
     */
    public function setDeliveryAddress($deliveryAddress)
    {
        $this->_deliveryAddress = $deliveryAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryAddress2()
    {
        return $this->_deliveryAddress2;
    }

    /**
     * @param string $deliveryAddress2
     * @return $this
     */
    public function setDeliveryAddress2($deliveryAddress2)
    {
        $this->_deliveryAddress2 = $deliveryAddress2;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryZipCode()
    {
        return $this->_deliveryZipCode;
    }

    /**
     * @param string $deliveryZipCode
     * @return $this
     */
    public function setDeliveryZipCode($deliveryZipCode)
    {
        $this->_deliveryZipCode = $deliveryZipCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryCity()
    {
        return $this->_deliveryCity;
    }

    /**
     * @param string $deliveryCity
     * @return $this
     */
    public function setDeliveryCity($deliveryCity)
    {
        $this->_deliveryCity = $deliveryCity;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryState()
    {
        return $this->_deliveryState;
    }

    /**
     * @param string $deliveryState
     * @return $this
     */
    public function setDeliveryState($deliveryState)
    {
        $this->_deliveryState = $deliveryState;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryCountryCode()
    {
        return $this->_deliveryCountryCode;
    }

    /**
     * @param string $deliveryCountryCode
     * @return $this
     */
    public function setDeliveryCountryCode($deliveryCountryCode)
    {
        $this->_deliveryCountryCode = $deliveryCountryCode;
        return $this;
    }

    /**
     * Sets both billing and delivery first name
     * @param string $value
     * @return $this
     */
    public function setFirstName($value)
    {
        $this->_billFirstName = $this->_deliveryFirstName = $value;
        return $this;
    }

    /**
     * Sets both billing and delivery last name
     * @param string $value
     * @return $this
     */
    public function setLastName($value)
    {
        $this->_billLastName = $this->_deliveryLastName = $value;
        return $this;
    }

    /**
     * Sets billing email
     * @param string $value
     * @return $this
     */
    public function setEmail($value)
    {
        $this->_billEmail = $value;
        return $this;
    }

    /**
     * Sets both billing and delivery phone
     * @param string $value
     * @return $this
     */
    public function setPhone($value)
    {
        $this->_billPhone = $this->_deliveryPhone = $value;
        return $this;
    }

    /**
     * Sets both billing and delivery address
     * @param string $value
     * @return $this
     */
    public function setAddress($value)
    {
        $this->_billAddress = $this->_deliveryAddress = $value;
        return $this;
    }

    /**
     * Sets second line for both billing and delivery address
     * @param string $value
     * @return $this
     */
    public function setAddress2($value)
    {
        $this->_billAddress2 = $this->_deliveryAddress2 = $value;
        return $this;
    }

    /**
     * Sets both billing and delivery zip code
     * @param string $value
     * @return $this
     */
    public function setZipCode($value)
    {
        $this->_billZipCode = $this->_deliveryZipCode = $value;
        return $this;
    }

    /**
     * Sets both billing and delivery city
     * @param string $value
     * @return $this
     */
    public function setCity($value)
    {
        $this->_billCity = $this->_deliveryCity = $value;
        return $this;
    }

    /**
     * Sets both billing and delivery state
     * @param string $value
     * @return $this
     */
    public function setState($value)
    {
        $this->_billState = $this->_deliveryState = $value;
        return $this;
    }

    /**
     * Sets both billing and delivery country code
     * @param string $value
     * @return $this
     */
    public function setCountryCode($value)
    {
        $this->_billCountryCode = $this->_deliveryCountryCode = $value;
        return $this;
    }

    #endregion
}