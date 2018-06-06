<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Enum\Currency;
use Travelhood\OtpSimplePay\Exception\OrderException;

/**
 * @property ProductCollectionInterface|ProductInterface[] $products
 */
class Order extends Component
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

    /** @var ProductCollectionInterface|ProductInterface[] */
    private $_products;

    protected $_orderRef = '';
    protected $_orderDate = '';
    protected $_pricesCurrency = '';
    protected $_orderShipping = 0;
    protected $_discount = 0;
    protected $_payMethod = '';
    protected $_language = '';

    protected $_billFirstName = '';
    protected $_billLastName = '';
    protected $_billEmail = '';
    protected $_billPhone = '';
    protected $_billAddress = '';
    protected $_billZipCode = '';
    protected $_billCity = '';
    protected $_billState = '';
    protected $_billCountryCode = '';

    protected $_deliveryFirstName = '';
    protected $_deliveryLastName = '';
    protected $_deliveryPhone = '';
    protected $_deliveryAddress = '';
    protected $_deliveryZipCode = '';
    protected $_deliveryCity = '';
    protected $_deliveryState = '';
    protected $_deliveryCountryCode = '';

    /**
     * @param Service $service
     * @param string $orderRef
     * @param string $orderDate
     */
    public function __construct(Service $service, $orderRef, $orderDate=null)
    {
        parent::__construct($service);
        $this->_products = new ProductCollection;
        $this->setOrderRef($orderRef);
        if(!$orderDate) {
            $orderDate = date('Y-m-d H:i:s');
        }
        $this->setOrderDate($orderDate);
        $this->setPricesCurrency($this->service->config->getCurrency());
        $this->setPayMethod(self::$DefaultPayMethod);
        $this->setLanguage(self::$DefaultLanguage);
    }

    public function __get($name)
    {
        switch($name) {
            case 'products':
                return $this->_products;
        }
        return parent::__get($name);
    }

    /**
     * @throws OrderException
     */
    public function validate()
    {
        foreach(['orderRef','pricesCurrency','payMethod', 'language'] as $k) {
            if(!$this->{'_'.$k}) {
                throw new OrderException('Missing field: '.$k);
            }
        }
        foreach(['FirstName', 'LastName', 'Phone', 'Email', 'Address', 'ZipCode', 'City', 'State', 'CountryCode'] as $k1) {
            foreach(['bill', 'delivery'] as $k2) {
                if($k1=='Email' && $k2=='delivery') {
                    continue;
                }
                $k = $k2.$k1;
                if(!$this->{'_'.$k}) {
                    throw new OrderException('Missing field: '.$k);
                }
            }
        }
        if($this->products->count() < 1) {
            throw new OrderException('No product specified');
        }
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
        foreach($this->products as $product) {
            $pname[] = $product->getName();
            $pcode[] = $product->getCode();
            $pinfo[] = $product->getInfo();
            $pprice[] = $product->getPrice();
            $pqty[] = $this->products->countProduct($product);
            $pvat[] = $product->getVat();
        }
        $query = '?order_ref='.urlencode($this->getOrderRef()).'&order_currency='.urlencode($this->service->config->getCurrency());
        $array = [
            'MERCHANT' => $this->service->config['merchant_id'],
            'ORDER_REF' => $this->getOrderRef(),
            'ORDER_DATE' => $this->getOrderDate(),
            'ORDER_PNAME' => $pname,
            'ORDER_PCODE' => $pcode,
            'ORDER_PINFO' => $pinfo,
            'ORDER_PRICE' => $pprice,
            'ORDER_QTY' => $pqty,
            'ORDER_VAT' => $pvat,
            'PRICES_CURRENCY' => $this->getPricesCurrency(),
            'ORDER_SHIPPING' => $this->getOrderShipping(),
            'DISCOUNT' => $this->getDiscount(),
            'PAY_METHOD' => $this->getPayMethod(),
            'LANGUAGE' => $this->getLanguage(),
            'ORDER_TIMEOUT' => $this->service->config['timeout'],
            'TIMEOUT_URL' => $this->service->config['url']['timeout'].$query,
            'BACK_REF' => $this->service->config['url']['back'].$query,
            'BILL_FNAME' => $this->getBillFirstName(),
            'BILL_LNAME' => $this->getBillLastName(),
            'BILL_EMAIL' => $this->getBillEmail(),
            'BILL_PHONE' => $this->getBillPhone(),
            'BILL_ADDRESS' => $this->getBillAddress(),
            'BILL_ZIPCODE' => $this->getBillZipCode(),
            'BILL_CITY' => $this->getBillCity(),
            'BILL_STATE' => $this->getBillState(),
            'BILL_COUNTRYCODE' => $this->getBillCountryCode(),
            'DELIVERY_FNAME' => $this->getDeliveryFirstName(),
            'DELIVERY_LNAME' => $this->getDeliveryLastName(),
            'DELIVERY_PHONE' => $this->getDeliveryPhone(),
            'DELIVERY_ADDRESS' => $this->getDeliveryAddress(),
            'DELIVERY_ZIPCODE' => $this->getDeliveryZipCode(),
            'DELIVERY_CITY' => $this->getDeliveryCity(),
            'DELIVERY_STATE' => $this->getDeliveryState(),
            'DELIVERY_COUNTRYCODE' => $this->getDeliveryCountryCode(),
        ];
        $serial = '';
        foreach(self::HASH_FIELDS as $field) {
            if(is_array($array[$field])) {
                foreach($array[$field] as $v) {
                    $serial.= strlen($v).$v;
                }
            }
            else {
                $v = $array[$field];
                $serial.= strlen($v).$v;
            }
        }
        $hash = $this->service->hasher->hashString($serial);
        $array['ORDER_HASH'] = $hash;
        return $array;
    }

    /**
     * @return string
     * @throws OrderException
     */
    public function __toString()
    {
        $s = $this->products.''.PHP_EOL;
        foreach($this->toArray() as $k=>$v) {
            if(is_array($v)) {
                continue;
            }
            $s.= $k.': '.$v.PHP_EOL;
        }
        return $s;
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
     * @return string
     */
    public function getPricesCurrency()
    {
        return $this->_pricesCurrency;
    }

    /**
     * @param string $pricesCurrency
     * @return $this
     */
    public function setPricesCurrency($pricesCurrency)
    {
        $this->_pricesCurrency = $pricesCurrency;
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
    public function setOrderShipping(int $orderShipping)
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
    public function setDiscount(int $discount)
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

    #endregion
}