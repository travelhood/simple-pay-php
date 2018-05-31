<?php

require_once __DIR__ . '/../bootstrap.php';
global $simplePay;

$order = $simplePay->createOrder('TO'.md5(microtime(true)));
$order->products->addProduct(new \Travelhood\OtpSimplePay\Product('Product 1', 'sku123', 'Some nice product', 5000, .27));
$order->products->addProduct(new \Travelhood\OtpSimplePay\Product('Product 2', 'sku456', 'Another awesome product', 10000, .14), 5);

$order
    ->setLanguage('HU')
    ->setFirstName('Lajos')
    ->setLastName('Bencz')
    ->setEmail('lajos.bencz@travelhood.com')
    ->setPhone('0611234567')
    ->setAddress('Main street 1.')
    ->setZipCode('1000')
    ->setCity('Budapest')
    ->setState('Budapest')
    ->setCountryCode('HU')
;

foreach($order->products as $product) {
    echo $product, '<br/>', PHP_EOL;
}

$liveUpdate = $simplePay->createLiveUpdate();
echo $liveUpdate->generateForm($order);
