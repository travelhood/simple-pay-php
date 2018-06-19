<?php

require_once __DIR__ . '/../../vendor/autoload.php';

global $simplePay;
$config = require __DIR__ . '/../fixture/config.php';
$config = new \Travelhood\OtpSimplePay\Config($config);
if(is_readable(__DIR__ . '/../fixture/config.local.php')) {
    $config->mergeConfig(require __DIR__ . '/../fixture/config.local.php');
}
$simplePay = new \Travelhood\OtpSimplePay\Service($config);
