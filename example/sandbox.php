<?php


function parse_http_response ($string)
{
    $headers = [];
//    $content = '';
    $str = strtok($string, "\n");
    $h = null;
    while ($str !== false) {
        if ($h and trim($str) === '') {
            $h = false;
            continue;
        }
        if ($h !== false and false !== strpos($str, ':')) {
            $h = true;
            list($headerName, $headerValue) = explode(':', trim($str), 2);
            $headerName = strtolower($headerName);
            $headerValue = ltrim($headerValue);
            if (isset($headers[$headerName]))
                $headers[$headerName] .= ',' . $headerValue;
            else
                $headers[$headerName] = $headerValue;
        }
//        if ($h === false) {
//            $content .= $str."\n";
//        }
        $str = strtok("\n");
    }
    return $headers;
}

require_once __DIR__ . '/../vendor/autoload.php';

const BASE_URL = 'https://unstable.travelhood.com/pay/';

$config = include __DIR__ . '/../test/fixture/config.php';
$config = new \Travelhood\OtpSimplePay\Config($config);
$config->setCurrency('HUF');

$orderId = '101010514872470318621';
$orderDate = '2017-02-16 12:10:31';

$data = [
    'MERCHANT' => $config['MERCHANT'],
    'ORDER_REF' => $orderId,
    'ORDER_DATE' => $orderDate,
    'PRICES_CURRENCY' => $config->getCurrency(),
    'ORDER_SHIPPING' => 0,
    'DISCOUNT' => 0,
    'PAY_METHOD' => 'CCVISAMC',
    'LANGUAGE' => 'HU',
    'ORDER_TIMEOUT' => 300,
    'TIMEOUT_URL' => BASE_URL.'timeout/?id='.$orderId.'&currency='.$config->getCurrency(),
    'BACK_REF' => BASE_URL.'back/?id='.$orderId.'&currency='.$config->getCurrency(),
];

$adr = [
    'FNAME' => 'Tester',
    'LNAME' => 'SimplePay',
//    'EMAIL' => 'email@example.com',
    'PHONE' => '36201234567',
    'ADDRESS' => 'First line address',
    'ZIPCODE' => '1234',
    'CITY' => 'City',
    'STATE' => 'State',
    'COUNTRYCODE' => 'HU',
];

foreach($adr as $ak=>$av) {
    foreach(['BILL', 'DELIVERY'] as $tk) {
        $data[$tk.'_'.$ak] = $av;
    }
}
$data['BILL_EMAIL'] = 'email@example.com';

$data['ORDER_PNAME'] = [
    'ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP #1',
    'ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP #2',
];
$data['ORDER_PCODE'] = [
    '123',
    '456',
];
$data['ORDER_PINFO'] = [
    'ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP',
    'ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP',
];
$data['ORDER_PRICE'] = [
    82000,
    57000,
];
$data['ORDER_QTY'] = [
    1,
    2,
];
$data['ORDER_VAT'] = [
    0,
    0,
];

$flatData = \Travelhood\OtpSimplePay\Util::flattenArray($data);
$hash = \Travelhood\OtpSimplePay\Util::hmacArray($flatData, $config['SECRET_KEY']);

$data['ORDER_HASH'] = $hash;

//print_r($data);

$curl = curl_init(\Travelhood\OtpSimplePay\Config::DEFAULTS['SANDBOX_URL'].\Travelhood\OtpSimplePay\Config::DEFAULTS['LU_URL']);
//$curl = curl_init('https://unstable.travelhood.com/pay/lu');
curl_setopt_array($curl, [
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => http_build_query($data),
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_VERBOSE, 0);
curl_setopt($curl, CURLOPT_HEADER, 1);

$response = curl_exec($curl);
$errno = curl_errno($curl);
$error = curl_error($curl);
if($errno !== 0) {
    throw new RuntimeException($error, $errno);
}

$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

if(is_resource($curl)) {
    curl_close($curl);
}

$header = parse_http_response(substr($response, 0, $header_size));
$body = substr($response, $header_size);
var_dump(['header'=>$header,'body'=>$body]);
