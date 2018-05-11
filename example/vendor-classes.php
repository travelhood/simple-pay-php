<?php

/**
 *  Copyright (C) 2016 OTP Mobil Kft.
 *
 *  PHP version 5
 *
 *  This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  SDK
 * @package   SimplePay_SDK
 * @author    SimplePay IT <itsupport@otpmobil.com>
 * @copyright 2016 OTP Mobil Kft.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
 * @version   1.0
 * @link      http://simplepartner.hu/online_fizetesi_szolgaltatas.html
 *
 */


/**
 * Base class for SimplePay implementation
 *
 * @category SDK
 * @package  SimplePay_SDK
 * @author   SimplePay IT <itsupport@otpmobil.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
 * @link     http://simplepartner.hu/online_fizetesi_szolgaltatas.html
 *
 */
class SimpleBase
{
    protected $merchantId;
    protected $secretKey;
    protected $hashCode;
    protected $hashString;
    protected $hashData = array();
    protected $runMode = 'LIVE';
    public $sdkVersion = 'SimplePay_PHP_SDK_1.0.2_160705';
    public $debug = false;
    public $logger = true;
    public $logPath = "log";
    public $hashFields = array();
    public $debugMessage = array();
    public $errorMessage = array();
    public $deniedInputChars = array("'", "\\", "\"");
    public $defaultsData = array(
        'BASE_URL' => "https://secure.simplepay.hu/payment/", //LIVE system
        'SANDBOX_URL' => "https://sandbox.simplepay.hu/payment/", //SANDBOX system
        'LU_URL' => "order/lu.php",   //relative to BASE_URL
        'ALU_URL' => "order/alu.php", //relative to BASE_URL
        'IDN_URL' => "order/idn.php", //relative to BASE_URL
        'IRN_URL' => "order/irn.php", //relative to BASE_URL
        'IOS_URL' => "order/ios.php", //relative to BASE_URL
        'OC_URL' => "order/tokens/"   //relative to BASE_URL
    );
    public $settings = array(
        'MERCHANT' => 'merchantId',
        'SECRET_KEY' => 'secretKey',
        'BASE_URL' => 'baseUrl',
        'ALU_URL' => 'aluUrl',
        'LU_URL' => 'luUrl',
        'IOS_URL' => 'iosUrl',
        'IDN_URL' => 'idnUrl',
        'IRN_URL' => 'irnUrl',
        'OC_URL' => 'ocUrl',
        'GET_DATA' => 'getData',
        'POST_DATA' => 'postData',
        'SERVER_DATA' => 'serverData',
        'PROTOCOL' => 'protocol',
        'SANDBOX' => 'sandbox',
        'CURL' => 'curl',
        'LOGGER' => 'logger',
        'LOG_PATH' => 'logPath',
        'DEBUG_LIVEUPDATE_PAGE' => 'debug_liveupdate_page',
        'DEBUG_LIVEUPDATE' => 'debug_liveupdate',
        'DEBUG_BACKREF' => 'debug_backref',
        'DEBUG_IPN' => 'debug_ipn',
        'DEBUG_IRN' => 'debug_irn',
        'DEBUG_IDN' => 'debug_idn',
        'DEBUG_IOS' => 'debug_ios',
        'DEBUG_ONECLICK' => 'debug_oneclick',
    );

    /**
     * Initialize MERCHANT, SECRET_KEY and CURRENCY
     *
     * @param string $config   Config array
     * @param string $currency Currency
     *
     * @return array $this->config Initialized config array
     *
     */
    public function merchantByCurrency($config = array(), $currency = '')
    {
        if (!is_array($config)) {
            $this->errorMessage[] = 'config is not array!';
            return false;
        } elseif (count($config) == 0) {
            $this->errorMessage[] = 'Empty config array!';
            return false;
        }

        $config['CURRENCY'] = str_replace(' ', '', $currency);
        $variables = array('MERCHANT', 'SECRET_KEY');

        foreach ($variables as $var) {
            if (isset($config[$currency . '_' . $var])) {
                $config[$var] = str_replace(' ', '', $config[$currency . '_' . $var]);
            } elseif (!isset($config[$currency . '_' . $var])) {
                $config[$var] = 'MISSING_' . $var;
                $this->errorMessage[] = 'Missing ' . $var;
            }
        }

        if ($this->debug) {
            foreach ($config as $configKey => $configValue) {
                if (strpos($configKey, 'SECRET_KEY') !== true) {
                    $this->debugMessage[] = $configKey . '=' . $configValue;
                }
            }
        }
        return $config;
    }

    /**
     * Initial settings
     *
     * @param array $config Array with config options
     *
     * @return boolean
     *
     */
    public function setup($config = array())
    {
        if (isset($config['SANDBOX'])) {
            if ($config['SANDBOX']) {
                $this->defaultsData['BASE_URL'] = $this->defaultsData['SANDBOX_URL'];
                $this->runMode = 'SANDBOX';
            }
        }
        $this->processConfig($this->defaultsData);
        $this->processConfig($config);

        if ($this->commMethod == 'liveupdate' && isset($config['BACK_REF'])) {
            $this->setField("BACK_REF", $config['BACK_REF']);
        }
        if ($this->commMethod == 'liveupdate' && isset($config['TIMEOUT_URL'])) {
            $this->setField("TIMEOUT_URL", $config['TIMEOUT_URL']);
        }
        return true;
    }

    /**
     * Set config options
     *
     * @param array $config Array with config options
     *
     * @return void
     *
     */
    public function processConfig($config = array())
    {
        foreach (array_keys($config) as $setting) {
            if (array_key_exists($setting, $this->settings)) {
                $prop = $this->settings[$setting];
                $this->$prop = $config[$setting];
            }
        }
    }

    /**
     * HMAC HASH creation
     *
     * @param string $key  Secret key for encryption
     * @param string $data String to encode
     *
     * @return string HMAC hash
     *
     */
    protected function hmac($key = '', $data = '')
    {
        if ($data == '') {
            $this->errorMessage[] = 'DATA FOR HMAC: MISSING!';
            return false;
        }
        if ($key == '') {
            $this->errorMessage[] = 'KEY FOR HMAC: MISSING!';
            return false;
        }
        return hash_hmac('md5', $data, trim($key));
    }

    /**
     * Create HASH code for an array (1-dimension only)
     *
     * @param array $hashData Array of ordered fields to be HASH-ed
     *
     * @return string Hash code
     *
     */
    protected function createHashString($hashData = array())
    {
        if (count($hashData) == 0) {
            $this->errorMessage[] = 'HASH_DATA: hashData is empty, so we can not generate hash string ';
            return false;
        }

        $hashString = '';
        $cunter = 1;
        foreach ($hashData as $field) {
            if (is_array($field)) {
                $this->errorMessage[] = 'HASH_ARRAY: No multi-dimension array allowed!';
                return false;
            }
            $hashString .= strlen(StripSlashes($field)).$field;
            $this->debugMessage[] = 'HASH_VALUE_' . $cunter .'('.strlen($field).'): '. $field;
            $cunter++;
        }

        $this->hashString = $hashString;
        $this->debugMessage[] = 'HASH string: ' . $this->hashString;
        $this->hashCode = $this->hmac($this->secretKey, $this->hashString);
        return $this->hashCode;
    }

    /**
     * Creates a 1-dimension array from a 2-dimension one
     *
     * @param array $array Array to be processed
     * @param array $skip  Array of keys to be skipped when creating the new array
     *
     * @return array $return Flat array
     *
     */
    public function flatArray($array = array(), $skip = array())
    {
        if (count($array) == 0) {
            $this->errorMessage[] = 'FLAT_ARRAY: array for flatArray is empty';
            return array();
        }
        $return = array();
        foreach ($array as $name => $item) {
            if (!in_array($name, $skip)) {
                if (is_array($item)) {
                    foreach ($item as $subItem) {
                        $return[] = $subItem;
                    }
                } elseif (!is_array($item)) {
                    $return[] = $item;
                }
            }
        }
        return $return;
    }

    /**
     * Write log
     *
     * @param string $state   State of the payment process
     * @param array  $data    Data of the log
     * @param string $orderId External ID of order
     *
     * @return void
     *
     */
    public function logFunc($state = '', $data = array(), $orderId = 0)
    {

        if ($this->logger) {
            $date = @date('Y-m-d H:i:s', time());
            $logFile = $this->logPath . '/' . @date('Ymd', time()) . '.log';

            if (!is_writable($this->logPath)) {
                $msg = 'LOG: log folder (' . $this->logPath . ') is not writable ';
                if (!in_array($msg, $this->debugMessage)) {
                    $this->debugMessage[] = $msg;
                }
                return false;
            }
            if (file_exists($logFile)) {
                if (!is_writable($logFile)) {
                    $msg = 'LOG: log file (' . $logFile . ') is not writable ';
                    if (!in_array($msg, $this->debugMessage)) {
                        $this->debugMessage[] = $msg;
                    }
                    return false;
                }
            }

            $logtext = $orderId . ' ' . $state . ' ' . $date . ' RUN_MODE=' . $this->runMode . "\n";
            foreach ($data as $logkey => $logvalue) {
                if (is_object($logvalue)) {
                    $logvalue = (array) $logvalue;
                }
                if (is_array($logvalue)) {
                    foreach ($logvalue as $subvalue) {
                        if (is_object($subvalue)) {
                            $subvalue = (array) $subvalue;
                        }
                        if (is_array($subvalue)) {
                            foreach ($subvalue as $subvalue2Key => $subvalue2Value) {
                                $logtext .= $orderId . ' ' . $state . ' ' . $date . ' ' . $subvalue2Key . '=' . $subvalue2Value . "\n";
                            }
                        }
                        else {
                            $logtext .= $orderId . ' ' . $state . ' ' . $date . ' ' . $logkey . '=' . $subvalue . "\n";
                        }
                    }
                } elseif (!is_array($logvalue)) {
                    $logtext .= $orderId . ' ' . $state . ' ' . $date . ' ' . $logkey . '=' . $logvalue . "\n";
                }
            }
            file_put_contents($logFile, $logtext, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Error logger
     *
     * @return void
     *
     */
    public function errorLogger()
    {
        switch ($this->commMethod) {
            case 'liveupdate':
                $orderId = $this->formData['ORDER_REF'];
                $type = "LiveUpdate";
                break;
            case 'backref':
                $orderId = $this->order_ref;
                $type = "BackRef";
                break;
            case 'ios':
                $orderId = $this->orderNumber;
                $type = "IOS";
                break;
            case 'ipn':
                $orderId = $this->postData['REFNOEXT'];
                $type = "IPN";
                break;
            case 'idn':
                $orderId = $this->refnoext;
                $type = "IDN";
                break;
            case 'irn':
                $orderId = $this->refnoext;
                $type = "IRN";
                break;
            case 'oneclick':
                $orderId = $this->formData['EXTERNAL_REF'];
                $type = "OneClick";
                break;

            default:
                $orderId = 'EMPTY';
                $type = 'general';
                $this->debugMessage[] = 'DEBUG_LOGGER_UNDEFINED_ID: ' . $orderId;
                $this->debugMessage[] = 'DEBUG_LOGGER_UNDEFINED_TYPE: ' . $type;
                break;
        }

        $errorCounter = count($this->errorMessage);

        $log = array();
        if ($this->debug || $errorCounter > 0) {
            $counter = 1;
            foreach ($this->debugMessage as $item) {
                $log['ITEM_' . $counter] = $item;
                $counter++;
            }
            $this->logFunc($type . '_DEBUG', $log, $orderId);
        }

        $log = array();
        if ($errorCounter > 0) {
            $counter = 1;
            foreach ($this->errorMessage as $item) {
                $log['ITEM_' . $counter] = $item;
                $counter++;
            }
            $this->logFunc($type . '_ERROR', $log, $orderId);
        }
    }

    /**
     * Returns string without extra characters
     *
     * @param string $string String for clean
     *
     * @return string $string
     *
     */
    public function cleanString($string = '')
    {
        return str_replace($this->deniedInputChars, '', $string);
    }

    /**
     * Prints all of error message
     *
     * @return void
     *
     */
    public function getErrorMessage()
    {
        $message = $this->getDebugMessage();
        $message .= '<font color="red">ERROR START</font><br>';
        foreach ($this->errorMessage as $items) {
            $message .= "-----------------------------------------------------------------------------------<br>";
            if (is_array($items) || is_object($items)) {
                $message .= "<pre>";
                $message .= $items;
                $message .= "</pre>";
            } elseif (!is_array($items) && !is_object($items)) {
                $message .= $items . '<br/>';
            }
            $message .= "-----------------------------------------------------------------------------------<br>";
        }
        $message .= '<font color="red">ERROR END</font><br>';
        iconv(mb_detect_encoding($message, mb_detect_order(), true), "UTF-8", $message);
        return $message;
    }

    /**
     * Prints all of debug elements
     *
     * @return void
     *
     */
    public function getDebugMessage()
    {
        $message = '<font color="red">DEBUG START</font><br>';
        foreach ($this->debugMessage as $items) {
            if (is_array($items) || is_object($items)) {
                $message .= "<pre>";
                $message .= print_r($items, true) . '<br/>';
                $message .= "</pre>";
            } elseif (!is_array($items) && !is_object($items)) {
                if (strpos($items, 'form action=') !== false) {
                    $message .= highlight_string($items, true) . '<br/>';
                } else {
                    $message .= $items . '<br/>';
                }
            }
        }

        if ($this->commMethod == 'liveupdate') {
            $message .= "-----------------------------------------------------------------------------------<br>";
            $message .= 'HASH FIELDS ' . print_r($this->hashFields, true);

            $message .= "-----------------------------------------------------------------------------------<br>";
            $message .= 'HASH DATA ' . print_r($this->hashData, true);

            $message .= "-----------------------------------------------------------------------------------<br>";
            $message .= highlight_string(@$this->luForm, true) . '<br/>';

            $message .= "-----------------------------------------------------------------------------------<br>";
            $message .= 'HASH CHECK ' . "<a href='http://hash.online-convert.com/md5-generator'>ONLINE HASH CONVERTER</a><br>";
        }
        $message .= "-----------------------------------------------------------------------------------<br>";
        $message .= '<font color="red">DEBUG END</font><br>';
        iconv(mb_detect_encoding($message, mb_detect_order(), true), "UTF-8", $message);
        return $message;
    }

}


/**
 * Class for SimplePay transaction handling
 *
 * @category SDK
 * @package  SimplePay_SDK
 * @author   SimplePay IT <itsupport@otpmobil.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
 * @link     http://simplepartner.hu/online_fizetesi_szolgaltatas.html
 *
 */
class SimpleTransaction extends SimpleBase
{
    public $result;
    public $targetUrl;
    public $baseUrl;
    public $curlInfo;
    public $formData = array();
    public $fieldData = array();
    public $missing = array();
    protected $products = array();
    protected $productFields = array('name', 'code', 'info', 'price', 'qty', 'vat');

    /**
     * Sends a HTTP request via cURL or file_get_contents() and returns the response
     *
     * @param string $url    Base URL for request
     * @param array  $data   Parameters to send
     * @param string $method Request method
     *
     * @return array $result Response
     *
     */
    public function startRequest($url = '', $data = array(), $method = 'POST')
    {
        $this->debugMessage[] = 'SEND START TIME' . ': ' . @date("Y-m-d H:i:s", time());
        $this->debugMessage[] = 'SEND METHOD' . ': ' . $method;
        $this->debugMessage[] = 'SEND URL' . ': ' . $url;
        foreach ($data as $dataKey => $dataValue) {
            $this->debugMessage[] = 'SEND DATA ' . $dataKey . ': ' . $dataValue;
        }
        if (!$this->curl) {
            //XML content
            $this->debugMessage[] = 'SEND WAY: file_get_contents';
            if (in_array("libxml", get_loaded_extensions())) {
                $options = array(
                    'http' => array(
                        'method' => $method,
                        'header' =>
                            "Accept-language: en\r\n".
                            "Content-type: application/x-www-form-urlencoded\r\n",
                        'content' => http_build_query($data, '', '&')
                    ));

                $context = stream_context_create($options);
                $result = @file_get_contents($url, true, $context);
                if (!$result) {
                    $this->errorMessage[] = 'file_get_contents() error.';
                    $this->errorMessage[] = 'Maybe your server (' . $this->serverData['SERVER_NAME'] . ') can not reach SimplePay service on file_get_contents() way.';
                }
                $this->debugMessage[] = 'SEND END TIME' . ': ' . @date("Y-m-d H:i:s", time());
                return $result;
            } elseif (!in_array("libxml", get_loaded_extensions())) {
                $this->errorMessage[] = 'libxml extension is missing or not activated.';
            }
        } elseif ($this->curl) {
            //cURL
            $this->debugMessage[] = 'SEND WAY: cURL';
            if (in_array("curl",  get_loaded_extensions())) {
                $curlData = curl_init();
                curl_setopt($curlData, CURLOPT_URL, $url);
                curl_setopt($curlData, CURLOPT_POST, true);
                if ($method != "POST") {
                    curl_setopt($curlData, CURLOPT_POST, false);
                }
                curl_setopt($curlData, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curlData, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curlData, CURLOPT_USERAGENT, 'curl');
                curl_setopt($curlData, CURLOPT_TIMEOUT, 60);
                curl_setopt($curlData, CURLOPT_FOLLOWLOCATION, true);
                //cURL + SSL
                //curl_setopt($curlData, CURLOPT_SSL_VERIFYPEER, false);
                //curl_setopt($curlData, CURLOPT_SSL_VERIFYHOST, false);
                $result = curl_exec($curlData);
                if (!$result) {
                    $this->errorMessage[] = 'cURL result error.';
                    $this->errorMessage[] = 'Maybe your server (' . $this->serverData['SERVER_NAME'] . ') can not reach SimplePay service on cURL() way.';
                }

                $this->curlInfo = curl_getinfo($curlData);
                foreach ($this->curlInfo as $curlKey => $curlValue) {
                    if (!is_array($curlValue)) {
                        $value = $curlValue;
                    } elseif (is_array($curlValue)) {
                        if (count($curlValue) == 0) {
                            $value = '';
                        } elseif (count($curlValue) > 0) {
                            foreach ($curlValue as $cvKey => $cvValue) {
                                $this->debugMessage[] = 'cURL_INFO ' . $curlKey . ' ' . $cvKey . ': ' . $cvValue;
                            }
                        }
                    }
                    $this->debugMessage[] = 'cURL_INFO ' . $curlKey . ': ' . $value;
                    if ($curlKey == 'http_code') {
                        if ($curlValue != 200) {
                            $this->errorMessage[] = 'cURL HTTP CODE is: ' . $curlValue;
                            $this->errorMessage[] = 'cURL URL: ' . $this->curlInfo['url'];
                        }
                    }
                }
                curl_close($curlData);
                $this->debugMessage[] = 'SEND END TIME' . ': ' . @date("Y-m-d H:i:s", time());
                return $result;
            } elseif (!in_array("curl",  get_loaded_extensions())) {
                $this->errorMessage[] = 'cURL extension is missing or not activated.';
            }
        }
        $this->errorMessage[] = 'SEND METHOD' . ': UNKNOWN';
        return false;
    }

    /**
     * Creates hidden HTML field
     *
     * @param string $name  Name of the field. ID parameter will be generated without "[]"
     * @param string $value Value of the field
     *
     * @return string HTML form element
     *
     */
    public function createHiddenField($name = '', $value = '')
    {
        if ($name == '') {
            $this->errorMessage[] = 'HTML HIDDEN: field name is empty';
            return false;
        }
        $inputId = $name;
        if (substr($name, -2, 2) == "[]") {
            $inputId = substr($name, 0, -2);
        }
        return "\n<input type='hidden' name='" . $name . "' id='" . $inputId . "' value='" . $value . "' />";
    }

    /**
     * Generates raw data array with HMAC HASH code for custom processing
     *
     * @param string $hashFieldName Index-name of the generated HASH field in the associative array
     *
     * @return array Data content of form
     *
     */
    public function createPostArray($hashFieldName = "ORDER_HASH")
    {
        if (!$this->prepareFields($hashFieldName)) {
            $this->errorMessage[] = 'POST ARRAY: Missing hash field name';
            return false;
        }
        return $this->formData;
    }

    /**
     * Sets default value for a field
     *
     * @param array $sets Array of fields and its parameters
     *
     * @return void
     *
     */
    protected function setDefaults($sets = array())
    {
        foreach ($sets as $set) {
            foreach ($set as $field => $fieldParams) {
                if ($fieldParams['type'] == 'single' && isset($fieldParams['default'])) {
                    $this->fieldData[$field] = $fieldParams['default'];
                }
            }
        }
    }

    /**
     * Checks if all required fields are set.
     * Returns true or the array of missing fields list
     *
     * @return boolean
     *
     */
    protected function checkRequired()
    {
        $missing = array();
        foreach ($this->validFields as $field => $params) {
            if (isset($params['required']) && $params['required']) {
                if ($params['type'] == "single") {
                    if (!isset($this->formData[$field])) {
                        $missing[] = $field;
                        $this->errorMessage[] = 'Missing field: ' . $field;
                    }
                } elseif ($params['type'] == "product") {
                    foreach ($this->products as $prod) {
                        $paramName = $params['paramName'];
                        if (!isset($prod[$paramName])) {
                            $missing[] = $field;
                            $this->errorMessage[] = 'Missing field: ' . $field;
                        }
                    }
                }
            }
        }
        $this->missing = $missing;
        return true;
    }

    /**
     * Getter method for fields
     *
     * @param string $fieldName Name of the field
     *
     * @return array Data of field
     *
     */
    public function getField($fieldName = '')
    {
        if (isset($this->fieldData[$fieldName])) {
            return $this->fieldData[$fieldName];
        }
        $this->debugMessage[] = 'GET FIELD: Missing field name in getField: ' . $fieldName;
        return false;
    }

    /**
     * Setter method for fields
     *
     * @param string $fieldName  Name of the field to be set
     * @param imxed  $fieldValue Value of the field to be set
     *
     * @return boolean
     *
     */
    public function setField($fieldName = '', $fieldValue = '')
    {
        if (in_array($fieldName, array_keys($this->validFields))) {
            $this->fieldData[$fieldName] = $this->cleanString($fieldValue);
            if ($fieldName == 'LU_ENABLE_TOKEN') {
                if ($fieldValue) {
                    $this->fieldData['LU_TOKEN_TYPE'] = 'PAY_BY_CLICK';
                }
            }
            return true;
        }
        $this->debugMessage[] = 'SET FIELD: Invalid field in setField: ' . $fieldName;
        return false;
    }

    /**
     * Adds product to the $this->product array
     *
     * @param mixed $product Array description of product or Product object
     *
     * @return void
     *
     */
    public function addProduct($product = array())
    {
        if (!is_array($product)) {
            $this->errorMessage[] = 'PRODUCT: Not a valid product!';
        }
        foreach ($this->productFields as $field) {
            if (array_key_exists($field, $product)) {
                $add[$field] = $this->cleanString($product[$field]);
            } elseif (!array_key_exists($field, $product)) {
                $add[$field] = ' ';
                $this->debugMessage[] = 'Missing product field: ' . $field;
            }
        }
        $this->products[] = $add;
    }


    /**
     * Finalizes and prepares fields for sending
     *
     * @param string $hashName Name of the field containing HMAC HASH code
     *
     * @return boolean
     *
     */
    protected function prepareFields($hashName = '')
    {
        if (!is_string($hashName)) {
            $this->errorMessage[] = 'PREPARE: Hash name is not string!';
            return false;
        }
        $this->setHashData();
        $this->setFormData();
        if ($this->hashData) {
            $this->formData[$hashName] = $this->createHashString($this->hashData);
        }
        $this->checkRequired();
        if (count($this->missing) == 0) {
            return true;
        }
        $this->debugMessage[] = 'PREPARE: Missing required fields';
        $this->errorMessage[] = 'PREPARE: Missing required fields';
        return false;
    }

    /**
     * Set hash data by hashFields
     *
     * @return void
     *
     */
    protected function setHashData()
    {
        foreach ($this->hashFields as $field) {
            $params = $this->validFields[$field];
            if ($params['type'] == "single") {
                if (isset($this->fieldData[$field])) {
                    $this->hashData[] = $this->fieldData[$field];
                }
            } elseif ($params['type'] == "product") {
                foreach ($this->products as $product) {
                    if (isset($product[$params["paramName"]])) {
                        $this->hashData[] = $product[$params["paramName"]];
                    }
                }
            }
        }
    }

    /**
     * Set form data by validFields
     *
     * @return void
     *
     */
    protected function setFormData()
    {
        foreach ($this->validFields as $field => $params) {
            if (isset($params["rename"])) {
                $field = $params["rename"];
            }
            if ($params['type'] == "single") {
                if (isset($this->fieldData[$field])) {
                    $this->formData[$field] = $this->fieldData[$field];
                }
            } elseif ($params['type'] == "product") {
                if (!isset($this->formData[$field])) {
                    $this->formData[$field] = array();
                }
                foreach ($this->products as $num => $product) {
                    if (isset($product[$params["paramName"]])) {
                        $this->formData[$field][$num] = $product[$params["paramName"]];
                    }
                }
            }
        }
    }

    /**
     * Finds and processes validation response from HTTP response
     *
     * @param string $resp HTTP response
     *
     * @return array Data
     *
     */
    public function processResponse($resp = '')
    {
        preg_match_all("/<EPAYMENT>(.*?)<\/EPAYMENT>/", $resp, $matches);
        $data = explode("|", $matches[1][0]);
        if (is_array($data)) {
            if (count($data) > 0) {
                $counter = 1;
                foreach ($data as $dataValue) {
                    $this->debugMessage[] = 'EPAYMENT_ELEMENT_' . $counter .': ' . $dataValue;
                    $counter++;
                }
            }
        }
        return $this->nameData($data);
    }

    /**
     * Validates HASH code of the response
     *
     * @param array $resp Array with the response data
     *
     * @return boolean
     *
     */
    public function checkResponseHash($resp = array())
    {
        $hash = $resp['ORDER_HASH'];
        array_pop($resp);
        $calculated = $this->createHashString($resp);
        $this->debugMessage[] = 'HASH ctrl: ' . $hash;
        $this->debugMessage[] = 'HASH calculated: ' . $calculated;
        if ($hash == $calculated) {
            $this->debugMessage[] = 'HASH CHECK: ' . 'Successful';
            return true;
        }
        $this->errorMessage[] = 'HASH ctrl: ' . $hash;
        $this->errorMessage[] = 'HASH calculated: ' . $calculated;
        $this->errorMessage[] = 'HASH CHECK: ' . 'Fail';
        $this->debugMessage[] = 'HASH CHECK: ' . 'Fail';
        return false;
    }
}


/**
 * SimplePay LiveUpdate
 *
 * Sending orders via HTTP request
 *
 * @category SDK
 * @package  SimplePay_SDK
 * @author   SimplePay IT <itsupport@otpmobil.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
 * @link     http://simplepartner.hu/online_fizetesi_szolgaltatas.html
 *
 */
class SimpleLiveUpdate extends SimpleTransaction
{
    public $formData = array();
    public $commMethod = 'liveupdate';
    protected $hashData = array();
    protected $validFields = array(
        //order
        "MERCHANT" => array("type" => "single", "paramName" => "merchantId", "required" => true),
        "ORDER_REF" => array("type" => "single", "required" => true),
        "ORDER_DATE" => array("type" => "single", "required" => true),
        "ORDER_PNAME" => array("type" => "product", "paramName" => "name"),
        "ORDER_PCODE" => array("type" => "product", "paramName" => "code"),
        "ORDER_PINFO" => array("type" => "product", "paramName" => "info"),
        "ORDER_PRICE" => array("type" => "product", "paramName" => "price", "required" => true),
        "ORDER_QTY" => array("type" => "product", "paramName" => "qty", "required" => true),
        "ORDER_VAT" => array("type" => "product", "default" => "0", "paramName" => "vat", "required" => true),
        "PRICES_CURRENCY" => array("type" => "single", "default" => "HUF", "required" => true),
        "ORDER_SHIPPING" => array("type" => "single", "default" => "0"),
        "DISCOUNT" => array("type" => "single", "default" => "0"),
        "PAY_METHOD" => array("type" => "single", "default" => "CCVISAMC", "required" => true),
        "LANGUAGE" => array("type" => "single", "default" => "HU"),
        "ORDER_TIMEOUT" => array("type" => "single", "default" => "300"),
        "TIMEOUT_URL" => array("type" => "single", "required" => true),
        "BACK_REF" => array("type" => "single", "required" => true),
        "LU_ENABLE_TOKEN" => array("type" => "single", "required" => false),
        "LU_TOKEN_TYPE" => array("type" => "single", "required" => false),

        //billing
        "BILL_FNAME" => array("type" => "single", "required" => true),
        "BILL_LNAME" => array("type" => "single", "required" => true),
        "BILL_COMPANY" => array("type" => "single"),
        "BILL_FISCALCODE" => array("type" => "single"),
        "BILL_EMAIL" => array("type" => "single", "required" => true),
        "BILL_PHONE" => array("type" => "single", "required" => true),
        "BILL_FAX" => array("type" => "single"),
        "BILL_ADDRESS" => array("type" => "single", "required" => true),
        "BILL_ADDRESS2" => array("type" => "single"),
        "BILL_ZIPCODE" => array("type" => "single", "required" => true),
        "BILL_CITY" => array("type" => "single", "required" => true),
        "BILL_STATE" => array("type" => "single", "required" => true),
        "BILL_COUNTRYCODE" => array("type" => "single", "required" => true),

        //delivery
        "DELIVERY_FNAME" => array("type" => "single", "required" => true),
        "DELIVERY_LNAME" => array("type" => "single", "required" => true),
        "DELIVERY_COMPANY" => array("type" => "single"),
        "DELIVERY_EMAIL" => array("type" => "single"),
        "DELIVERY_PHONE" => array("type" => "single", "required" => true),
        "DELIVERY_ADDRESS" => array("type" => "single", "required" => true),
        "DELIVERY_ADDRESS2" => array("type" => "single"),
        "DELIVERY_ZIPCODE" => array("type" => "single", "required" => true),
        "DELIVERY_CITY" => array("type" => "single", "required" => true),
        "DELIVERY_STATE" => array("type" => "single", "required" => true),
        "DELIVERY_COUNTRYCODE" => array("type" => "single", "required" => true),
    );

    //hash fields
    public $hashFields = array(
        "MERCHANT",
        "ORDER_REF",
        "ORDER_DATE",
        "ORDER_PNAME",
        "ORDER_PCODE",
        "ORDER_PINFO",
        "ORDER_PRICE",
        "ORDER_QTY",
        "ORDER_VAT",
        "ORDER_SHIPPING",
        "PRICES_CURRENCY",
        "DISCOUNT",
        "PAY_METHOD"
    );

    /**
     * Constructor of SimpleLiveUpdate class
     *
     * @param array  $config   Configuration array or filename
     * @param string $currency Transaction currency
     *
     * @return void
     *
     */
    public function __construct($config = array(), $currency = '')
    {
        $this->setDefaults(array($this->validFields));
        $config = $this->merchantByCurrency($config, $currency);
        $this->setup($config);
        if (isset($this->debug_liveupdate)) {
            $this->debug = $this->debug_liveupdate;
        }
        $this->setField("PRICES_CURRENCY", $currency);
        $this->setField("ORDER_DATE", @date("Y-m-d H:i:s"));
        $this->fieldData['MERCHANT'] = $this->merchantId;
        $this->debugMessage[] = 'MERCHANT: ' . $this->fieldData['MERCHANT'];
        $this->targetUrl = $this->luUrl;
    }


    /**
     * Generates a ready-to-insert HTML FORM
     *
     * @param string $formName          The ID parameter of the form
     * @param string $submitElement     The type of the submit element ('button' or 'link')
     * @param string $submitElementText The label for the submit element
     *
     * @return string HTML form
     *
     */
    public function createHtmlForm($formName = 'SimplePayForm', $submitElement = 'button', $submitElementText = 'Start Payment')
    {
        if (count($this->errorMessage) > 0) {
            return false;
        }
        if (!$this->prepareFields("ORDER_HASH")) {
            $this->errorMessage[] = 'HASH FIELD: Missing hash field name';
            return false;
        }

        $logString = "";
        $this->luForm = "\n<form action='" . $this->baseUrl . $this->targetUrl . "' method='POST' id='" . $formName . "' accept-charset='UTF-8'>";
        foreach ($this->formData as $name => $field) {
            if (is_array($field)) {
                foreach ($field as $subField) {
                    $this->luForm .= $this->createHiddenField($name . "[]", $subField);
                    $logString .= $name . '=' . $subField . "\n";
                }
            } elseif (!is_array($field)) {
                if ($name == "BACK_REF" or $name == "TIMEOUT_URL") {
                    $concat = '?';
                    if (strpos($field, '?') !== false) {
                        $concat = '&';
                    }
                    $field .= $concat . 'order_ref=' . $this->fieldData['ORDER_REF'] . '&order_currency=' . $this->fieldData['PRICES_CURRENCY'];
                    $field = $this->protocol . '://' . $field;
                }
                $this->luForm .= $this->createHiddenField($name, $field);
                $logString .= $name . '=' . $field . "\n";
            }
        }
        $this->luForm .= $this->createHiddenField("SDK_VERSION", $this->sdkVersion);
        $this->luForm .= $this->formSubmitElement($formName, $submitElement, $submitElementText);
        $this->luForm .= "\n</form>";
        $this->logFunc("LiveUpdate", $this->formData, $this->formData['ORDER_REF']);
        $this->debugMessage[] = 'HASH CODE: ' . $this->hashCode;
        return $this->luForm;
    }


    /**
     * Generates HTML submit element
     *
     * @param string $formName          The ID parameter of the form
     * @param string $submitElement     The type of the submit element ('button' or 'link')
     * @param string $submitElementText The lebel for the submit element
     *
     * @return string HTML submit
     *
     */
    protected function formSubmitElement($formName = '', $submitElement = 'button', $submitElementText = '')
    {
        switch ($submitElement) {
            case 'link':
                $element = "\n<a href='javascript:document.getElementById(\"" . $formName ."\").submit()'>".addslashes($submitElementText)."</a>";
                break;
            case 'button':
                $element = "\n<button type='submit'>".addslashes($submitElementText)."</button>";
                break;
            case 'auto':
                $element = "\n<button type='submit'>".addslashes($submitElementText)."</button>";
                $element .= "\n<script language=\"javascript\" type=\"text/javascript\">document.getElementById(\"" . $formName . "\").submit();</script>";
                break;
            default :
                $element = "\n<button type='submit'>".addslashes($submitElementText)."</button>";
                break;
        }
        return $element;
    }
}


/**
 * SimplePay BACK_REF
 *
 * Processes information sent via HTTP GET on the returning site after a payment
 *
 * @category SDK
 * @package  SimplePay_SDK
 * @author   SimplePay IT <itsupport@otpmobil.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
 * @link     http://simplepartner.hu/online_fizetesi_szolgaltatas.html
 *
 */
class SimpleBackRef extends SimpleTransaction
{
    protected $backref;
    public $commMethod = 'backref';
    public $protocol;
    protected $request;
    protected $returnVars = array(
        "RC",
        "RT",
        "3dsecure",
        "date",
        "payrefno",
        "ctrl"
    );
    public $backStatusArray = array(
        'BACKREF_DATE' => 'N/A',
        'REFNOEXT' => 'N/A',
        'PAYREFNO' => 'N/A',
        'ORDER_STATUS' => 'N/A',
        'PAYMETHOD' => 'N/A',
        'RESULT' => false
    );
    public $successfulStatus = array(
        "IN_PROGRESS",          //card authorized on backref
        "PAYMENT_AUTHORIZED",   //IPN
        "COMPLETE",             //IDN
        "WAITING_PAYMENT",      //waiting for WIRE
    );
    public  $unsuccessfulStatus = array(
        "CARD_NOTAUTHORIZED",   //unsuccessful transaction
        "FRAUD",
        "TEST"
    );

    /**
     * Constructor of SimpleBackRef class
     *
     * @param array  $config   Configuration array or filename
     * @param string $currency Transaction currency
     *
     * @return void
     *
     */
    public function __construct($config = array(), $currency = '')
    {
        $config = $this->merchantByCurrency($config, $currency);
        $this->iosConfig = $config;
        $this->setup($config);
        if (isset($this->debug_backref)) {
            $this->debug = $this->debug_backref;
        }
        $this->createRequestUri();
        $this->backStatusArray['BACKREF_DATE'] = (isset($this->getData['date'])) ? $this->getData['date'] : 'N/A';
        $this->backStatusArray['REFNOEXT'] = (isset($this->getData['order_ref'])) ? $this->getData['order_ref'] : 'N/A';
        $this->backStatusArray['PAYREFNO'] = (isset($this->getData['payrefno'])) ? $this->getData['payrefno'] : 'N/A';
    }

    /**
     * Creates request URI from HTTP SERVER VARS.
     * Handles http and https
     *
     * @return void
     *
     */
    protected function createRequestUri()
    {
        if ($this->protocol == '') {
            $this->protocol = "http";
        }
        $this->request = $this->protocol . '://' . $this->serverData['HTTP_HOST'] . $this->serverData['REQUEST_URI'];
        $this->debugMessage[] = 'REQUEST: ' . $this->request;
    }

    /**
     * Validates CTRL variable
     *
     * @return boolean
     *
     */
    protected function checkCtrl()
    {
        $requestURL = substr($this->request, 0, -38); //the last 38 characters are the CTRL param
        $hashInput = strlen($requestURL) . $requestURL;
        $this->debugMessage[] = 'REQUEST URL: ' . $requestURL;
        $this->debugMessage[] = 'GET ctrl: ' . @$this->getData['ctrl'];
        $this->debugMessage[] = 'Calculated ctrl: ' . $this->hmac($this->secretKey, $hashInput);
        if (isset($this->getData['ctrl']) && $this->getData['ctrl'] == $this->hmac($this->secretKey, $hashInput)) {
            return true;
        }
        $this->errorMessage[] = 'HASH: Calculated hash is not valid!';
        $this->errorMessage[] = 'BACKREF ERROR: ' . @$this->getData['err'];
        return false;
    }

    /**
     * Check card authorization response
     *
     * 1. check ctrl
     * 2. check RC & RT
     * 3. check IOS status
     *
     * @return boolean
     *
     */
    public function checkResponse()
    {
        if (!isset($this->order_ref)) {
            $this->errorMessage[] = 'CHECK RESPONSE: Missing order_ref variable!';
            return false;
        }
        $this->logFunc("BackRef", $this->getData, $this->order_ref);

        if (!$this->checkCtrl()) {
            $this->errorMessage[] = 'CHECK RESPONSE: INVALID CTRL!';
            return false;
        }

        $ios = new SimpleIos($this->iosConfig, $this->getData['order_currency'], $this->order_ref);

        foreach ($ios->errorMessage as $msg) {
            $this->errorMessage[] = $msg;
        }
        foreach ($ios->debugMessage as $msg) {
            $this->debugMessage[] = $msg;
        }

        if (is_object($ios)) {
            $this->checkIOSStatus($ios);
        }
        $this->logFunc("BackRef_BackStatus", $this->backStatusArray, $this->order_ref);
        if (!$this->checkRtVariable($ios)) {
            return false;
        }
        if (!$this->backStatusArray['RESULT']) {
            return false;
        }
        return true;
    }

    /**
     * Check IOS result
     *
     * @param obj $ios Result of IOS comunication
     *
     * @return boolean
     *
     */
    protected function checkIOSStatus($ios)
    {
        $this->backStatusArray['ORDER_STATUS'] = (isset($ios->status['ORDER_STATUS'])) ? $ios->status['ORDER_STATUS'] : 'IOS_ERROR';
        $this->backStatusArray['PAYMETHOD'] = (isset($ios->status['PAYMETHOD'])) ? $ios->status['PAYMETHOD'] : 'N/A';
        if (in_array(trim($ios->status['ORDER_STATUS']), $this->successfulStatus)) {
            $this->backStatusArray['RESULT'] = true;
        } elseif (in_array(trim($ios->status['ORDER_STATUS']), $this->unsuccessfulStatus)) {
            $this->backStatusArray['RESULT'] = false;
            $this->errorMessage[] = 'IOS STATUS: UNSUCCESSFUL!';
        }
    }

    /**
     * Check RT variable
     *
     * @param obj $ios Result of IOS comunication
     *
     * @return boolean
     *
     */
    protected function checkRtVariable($ios)
    {
        if (isset($this->getData['RT'])) {
            //000 and 001 are successful
            if (in_array(substr($this->getData['RT'], 0, 3), array("000", "001"))) {
                $this->backStatusArray['RESULT'] = true;
            } elseif ($this->getData['RT'] == "") {
                //check IOS ORDER_STATUS
                if (in_array(trim($ios->status['ORDER_STATUS']), $this->successfulStatus)) {
                    $this->backStatusArray['RESULT'] = true;
                    return true;
                }
            }
        }
        if (!isset($this->getData['RT'])) {
            $this->backStatusArray['RESULT'] = false;
            $this->errorMessage[] = 'Missing variables: (RT)!';
            return false;
        }
        return true;
    }
}


/**
 * SimpleIOS
 *
 * Helper object containing information about a product
 *
 * @category SDK
 * @package  SimplePay_SDK
 * @author   SimplePay IT <itsupport@otpmobil.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
 * @link     http://simplepartner.hu/online_fizetesi_szolgaltatas.html
 *
 */
class SimpleIos extends SimpleTransaction
{
    protected $orderNumber;
    protected $merchantId;
    protected $orderStatus;
    protected $maxRun = 10;
    protected $iosOrderUrl = '';
    public $commMethod = 'ios';
    public $status = Array();
    public $errorMessage = Array();
    public $debugMessage = Array();

    /**
     * Constructor of SimpleIos class
     *
     * @param array  $config      Configuration array or filename
     * @param string $currency    Transaction currency
     * @param string $orderNumber External number of the order
     *
     * @return void
     *
     */
    public function __construct($config = array(), $currency = '', $orderNumber = '0')
    {
        $config = $this->merchantByCurrency($config, $currency);
        $this->setup($config);
        if (isset($this->debug_ios)) {
            $this->debug = $this->debug_ios;
        }
        $this->orderNumber = $orderNumber;
        $this->iosOrderUrl = $this->defaultsData['BASE_URL'] . $this->defaultsData['IOS_URL'];
        $this->runIos();
        $this->logFunc("IOS", $this->status, $this->orderNumber);
    }

    /**
     * Starts IOS communication
     *
     * @return void
     *
     */
    public function runIos()
    {
        $this->debugMessage[] = 'IOS: START';
        $iosArray = array(
            'MERCHANT' => $this->merchantId,
            'REFNOEXT' => $this->orderNumber,
            'HASH' => $this->createHashString(array($this->merchantId, $this->orderNumber))
        );
        $this->logFunc("IOS", $iosArray, $this->orderNumber);
        $iosCounter = 0;
        while ($iosCounter < $this->maxRun) {
            $result = $this->startRequest($this->iosOrderUrl, $iosArray, 'POST');
            if ($result === false) {
                $result = '<?xml version="1.0"?>
                <Order>
                    <ORDER_DATE>' . @date("Y-m-d H:i:s", time()) . '</ORDER_DATE>
                    <REFNO>N/A</REFNO>
                    <REFNOEXT>N/A</REFNOEXT>
                    <ORDER_STATUS>EMPTY RESULT</ORDER_STATUS>
                    <PAYMETHOD>N/A</PAYMETHOD>
                    <HASH>N/A</HASH>
                </Order>';
            }

            $resultArray = (array) simplexml_load_string($result);
            foreach ($resultArray as $itemName => $itemValue) {
                $this->status[$itemName] = $itemValue;
            }
            switch ($this->status['ORDER_STATUS']) {
                case 'NOT_FOUND':
                    $iosCounter++;
                    sleep(1);
                    break;
                case 'CARD_NOTAUTHORIZED':
                    $iosCounter += 5;
                    sleep(1);
                    break;
                default:
                    $iosCounter += $this->maxRun;
            }
            $this->debugMessage[] = 'IOS ORDER_STATUS: ' . $this->status['ORDER_STATUS'];
        }
        $this->debugMessage[] = 'IOS: END';
    }
}


/**
 * SimplePay Instant Payment Notification
 *
 * Processes notifications sent via HTTP POST request
 *
 * @category SDK
 * @package  SimplePay_SDK
 * @author   SimplePay IT <itsupport@otpmobil.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
 * @link     http://simplepartner.hu/online_fizetesi_szolgaltatas.html
 *
 */
class SimpleIpn extends SimpleBase
{
    public $echo = true;
    public $commMethod = 'ipn';
    public $successfulStatus = array(
        "PAYMENT_AUTHORIZED",   //IPN
        "COMPLETE",             //IDN
        "REFUND",               //IRN
        "PAYMENT_RECEIVED",     //WIRE
    );

    /**
     * Constructor of SimpleIpn class
     *
     * @param mixed  $config   Configuration array or filename
     * @param string $currency Transaction currency
     *
     * @return void
     *
     */
    public function __construct($config = array(), $currency = '')
    {
        $config = $this->merchantByCurrency($config, $currency);
        $this->setup($config);
        if (isset($this->debug_ipn)) {
            $this->debug = $this->debug_ipn;
        }
    }

    /**
     * Validate recceived data against HMAC HASH
     *
     * @return boolean
     *
     */
    public function validateReceived()
    {
        $this->debugMessage[] = 'IPN VALIDATION: START';
        $this->logFunc("IPN", $this->postData, $this->postData['REFNOEXT']);
        if (!in_array(trim($this->postData['ORDERSTATUS']), $this->successfulStatus)) {
            $this->errorMessage[] = 'INVALID IPN ORDER STATUS: ' . $this->postData['ORDERSTATUS'];
            $this->debugMessage[] = 'IPN VALIDATION: END';
            return false;
        }
        $validationResult = false;
        $calculatedHashString = $this->createHashString($this->flatArray($this->postData, array("HASH")));
        if ($calculatedHashString == $this->postData['HASH']) {
            $validationResult = true;
        }
        if ($validationResult) {
            $this->debugMessage[] = 'IPN VALIDATION: ' . 'SUCCESSFUL';
            $this->debugMessage[] = 'IPN CALCULATED HASH: ' . $calculatedHashString;
            $this->debugMessage[] = 'IPN HASH: ' . $this->postData['HASH'];
            $this->debugMessage[] = 'IPN VALIDATION: END';
            return true;
        } elseif (!$validationResult) {
            $this->errorMessage[] = 'IPN VALIDATION: ' . 'FAILED';
            $this->errorMessage[] = 'IPN CALCULATED HASH: ' . $calculatedHashString;
            $this->errorMessage[] = 'IPN RECEIVED HASH: ' . $this->postData['HASH'];
            $this->debugMessage[] = 'IPN VALIDATION: END';
            return false;
        }
        return false;
    }

    /**
     * Creates INLINE string for corfirmation
     *
     * @return string $string <EPAYMENT> tag
     *
     */
    public function confirmReceived()
    {
        $this->debugMessage[] = 'IPN CONFIRM: START';
        $serverDate = date("YmdHis");
        $hashArray = array(
            $this->postData['IPN_PID'][0],
            $this->postData['IPN_PNAME'][0],
            $this->postData['IPN_DATE'],
            $serverDate
        );
        $hash = $this->createHashString($hashArray);
        $string = "<EPAYMENT>" . $serverDate . "|" . $hash . "</EPAYMENT>";
        $this->debugMessage[] = 'IPN CONFIRM EPAYMENT: ' . $string;
        $this->debugMessage[] = 'IPN CONFIRM: END';
        if ($this->echo) {
            echo $string;
        }
        return $string;
    }
}


/**
 * SimplePay Instant Delivery Information
 *
 * Sends delivery notification via HTTP
 *
 * @category SDK
 * @package  SimplePay_SDK
 * @author   SimplePay IT <itsupport@otpmobil.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
 * @link     http://simplepartner.hu/online_fizetesi_szolgaltatas.html
 *
 */
class SimpleIdn extends SimpleTransaction
{
    public $targetUrl = '';
    public $commMethod = 'idn';
    public $idnRequest = array();
    public $hashFields = array(
        "MERCHANT",
        "ORDER_REF",
        "ORDER_AMOUNT",
        "ORDER_CURRENCY",
        "IDN_DATE"
    );

    protected $validFields = array(
        "MERCHANT" => array("type"=>"single", "paramName"=>"merchantId", "required" => true),
        "ORDER_REF" => array("type"=>"single", "paramName"=>"orderRef", "required"=>true),
        "ORDER_AMOUNT" => array("type"=>"single", "paramName"=>"amount", "required"=>true),
        "ORDER_CURRENCY" => array("type"=>"single", "paramName"=>"currency", "required"=>true),
        "IDN_DATE" => array("type"=>"single", "paramName"=>"idnDate", "required"=>true),
        "REF_URL" => array("type"=>"single", "paramName"=>"refUrl"),
    );

    /**
     * Constructor of SimpleIdn class
     *
     * @param mixed  $config   Configuration array or filename
     * @param string $currency Transaction currency
     *
     * @return void
     *
     */
    public function __construct($config = array(), $currency = '')
    {
        $config = $this->merchantByCurrency($config, $currency);
        $this->setup($config);
        if (isset($this->debug_idn)) {
            $this->debug = $this->debug_idn;
        }
        $this->fieldData['MERCHANT'] = $this->merchantId;
        $this->targetUrl = $this->defaultsData['BASE_URL'] . $this->defaultsData['IDN_URL'];
    }

    /**
     * Creates associative array for the received data
     *
     * @param array $data Processed data
     *
     * @return void
     *
     */
    protected function nameData($data = array())
    {
        return array(
            "ORDER_REF" => (isset($data[0])) ? $data[0] : 'N/A',
            "RESPONSE_CODE" => (isset($data[1])) ? $data[1] : 'N/A',
            "RESPONSE_MSG" => (isset($data[2])) ? $data[2] : 'N/A',
            "IDN_DATE" => (isset($data[3])) ? $data[3] : 'N/A',
            "ORDER_HASH" => (isset($data[4])) ? $data[4] : 'N/A',
        );
    }

    /**
     * Sends notification via cURL
     *
     * @param array $data Data array to be sent
     *
     * @return array $this->nameData() Result
     *
     */
    public function requestIdn($data = array())
    {
        if (count($data) == 0) {
            $this->errorMessage[] = 'IDN DATA: EMPTY';
            return $this->nameData();
        }
        $data['MERCHANT'] = $this->merchantId;
        $this->refnoext = $data['REFNOEXT'];
        unset($data['REFNOEXT']);

        foreach ($this->hashFields as $fieldKey) {
            $data2[$fieldKey] = $data[$fieldKey];
        }
        $irnHash = $this->createHashString($data2);
        $data2['ORDER_HASH'] = $irnHash;
        $this->idnRequest = $data2;
        $this->logFunc("IDN", $this->idnRequest, $this->refnoext);

        $result = $this->startRequest($this->targetUrl, $this->idnRequest, 'POST');
        $this->debugMessage[] = 'IDN RESULT: ' . $result;

        if (is_string($result)) {
            $processed = $this->processResponse($result);
            $this->logFunc("IDN", $processed, $this->refnoext);
            return     $processed;
        }
        $this->debugMessage[] = 'IDN RESULT: NOT STRING';
        return false;
    }
}


/**
 * SimplePay Instant Refund Notification
 *
 * Sends Refund request via HTTP request
 *
 * @category SDK
 * @package  SimplePay_SDK
 * @author   SimplePay IT <itsupport@otpmobil.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
 * @link     http://simplepartner.hu/online_fizetesi_szolgaltatas.html
 *
 */
class SimpleIrn extends SimpleTransaction
{
    public $targetUrl = '';
    public $commMethod = 'irn';
    public $irnRequest = array();
    public $hashFields = array(
        "MERCHANT",
        "ORDER_REF",
        "ORDER_AMOUNT",
        "ORDER_CURRENCY",
        "IRN_DATE",
        "AMOUNT"
    );

    protected $validFields = array(
        "MERCHANT" => array("type" => "single", "paramName" => "merchantId", "required" => true),
        "ORDER_REF" => array("type" => "single", "paramName" => "orderRef", "required" => true),
        "ORDER_AMOUNT" => array("type" => "single", "paramName" => "amount", "required" => true),
        "AMOUNT" => array("type" => "single", "paramName" => "amount", "required" => true),
        "ORDER_CURRENCY" => array("type" => "single", "paramName" => "currency", "required" => true),
        "IRN_DATE" => array("type" => "single", "paramName" => "irnDate", "required" => true),
    );

    /**
     * Constructor of SimpleIrn class
     *
     * @param mixed  $config   Configuration array or filename
     * @param string $currency Transaction currency
     *
     * @return void
     *
     */
    public function __construct($config = array(), $currency = '')
    {
        $config = $this->merchantByCurrency($config, $currency);
        $this->setup($config);
        if (isset($this->debug_irn)) {
            $this->debug = $this->debug_irn;
        }
        $this->fieldData['MERCHANT'] = $this->merchantId;
        $this->targetUrl = $this->defaultsData['BASE_URL'] . $this->defaultsData['IRN_URL'];
    }

    /**
     * Creates associative array for the received data
     *
     * @param array $data Processed data
     *
     * @return void
     *
     */
    protected function nameData($data = array())
    {
        return array(
            "ORDER_REF" => (isset($data[0])) ? $data[0] : 'N/A',
            "RESPONSE_CODE" => (isset($data[1])) ? $data[1] : 'N/A',
            "RESPONSE_MSG" => (isset($data[2])) ? $data[2] : 'N/A',
            "IRN_DATE" => (isset($data[3])) ? $data[3] : 'N/A',
            "ORDER_HASH" => (isset($data[4])) ? $data[4] : 'N/A',
        );
    }

    /**
     * Sends notification via cURL
     *
     * @param array $data (Optional) Data array to be sent
     *
     * @return array $this->nameData() Result
     *
     */
    public function requestIrn($data = array())
    {
        if (count($data) == 0) {
            $this->errorMessage[] = 'IRN DATA: EMPTY';
            return $this->nameData();
        }
        $data['MERCHANT'] = $this->merchantId;
        $this->refnoext = $data['REFNOEXT'];
        unset($data['REFNOEXT']);

        foreach ($this->hashFields as $fieldKey) {
            $data2[$fieldKey] = $data[$fieldKey];
        }
        $irnHash = $this->createHashString($data2);
        $data2['ORDER_HASH'] = $irnHash;
        $this->irnRequest = $data2;
        $this->logFunc("IRN", $this->irnRequest, $this->refnoext);

        $result = $this->startRequest($this->targetUrl, $this->irnRequest, 'POST');
        $this->debugMessage[] = 'IRN RESULT: ' . $result;

        if (is_string($result)) {
            $processed = $this->processResponse($result);
            $this->logFunc("IRN", $processed, $this->refnoext);
            return $processed;
        }
        $this->debugMessage[] = 'IRN RESULT: NOT STRING';
        return false;
    }
}
