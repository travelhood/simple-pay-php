<?php

require_once __DIR__ . '/../bootstrap.php';
global $simplePay;

echo $simplePay->pagePaymentNotification();

echo '<hr/><a href="/">Back</a>';
