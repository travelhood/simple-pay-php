<?php

require_once __DIR__ . '/../bootstrap.php';
global $simplePay;

echo $simplePay->pageInstantPaymentNotification();

echo '<a href="/">Back</a>';
