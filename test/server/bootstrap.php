<?php

require_once __DIR__ . '/../../vendor/autoload.php';

global $simplePay;
$config = require __DIR__ . '/../fixture/config.php';
$config = new \Travelhood\OtpSimplePay\Config($config);
$simplePay = new \Travelhood\OtpSimplePay\Service($config);
