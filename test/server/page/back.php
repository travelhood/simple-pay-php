<?php

require_once __DIR__ . '/../bootstrap.php';
global $simplePay;

$page = $simplePay->pageBack();

echo $page;

$status = $simplePay->instantOrderStatus($page->getOrderRef(), $page->getOrderCurrency());

echo $status->getOrderStatus(), '<br/>';
echo $status->getResponseText(), '<br/>';

echo '<hr/><a href="/">Back</a>';
