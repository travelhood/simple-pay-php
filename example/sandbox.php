<?php

require_once __DIR__ . '/../vendor/autoload.php';

$url = 'https://sandbox.simplepay.hu/payment/order/ios.php';
$data = array(
    'MERCHANT' => 'PUBLICTESTHUF',
    'REFNOEXT' => '101010514615913074586',
    //'HASH' => '9607a566c832821b8447eea204e6da1e'
);

$hash = \Travelhood\OtpSimplePay\Util::hmacArray($data, 'FxDa5w314kLlNseq2sKuVwaqZshZT5d6');
$data['HASH'] = $hash;

var_dump($data);

$curlData = curl_init();
curl_setopt($curlData, CURLOPT_URL, $url);
curl_setopt($curlData, CURLOPT_POST, true);
curl_setopt($curlData, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($curlData, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curlData, CURLOPT_USERAGENT, 'curl');
curl_setopt($curlData, CURLOPT_TIMEOUT, 60);
curl_setopt($curlData, CURLOPT_FOLLOWLOCATION, true);
$resultXml = curl_exec($curlData);
curl_close($curlData);
$result = (array)simplexml_load_string($resultXml);
var_dump($result);

exit;

require_once __DIR__ . '/vendor-classes.php';

function ios($orderNumber)
{
    $ios = new SimpleIos([
        'HUF_MERCHANT' => 'PUBLICTESTHUF',
        'HUF_SECRET_KEY' => 'FxDa5w314kLlNseq2sKuVwaqZshZT5d6',
    ], 'HUF', $orderNumber);
    $ios->runIos();
    var_dump($ios->status);
}


foreach (['101010514615913074586', 'TOf945f691040b77a29f19b35c6dbbd5e9', '99181132', 'TOcc2b7511c5798f0e4587ee936a749a69', '99180427'] as $orderNumber) {
    ios($orderNumber);
}

