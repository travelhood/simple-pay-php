<?php

class BaseTest extends \PHPUnit\Framework\TestCase
{
    const P1 = [
        'name' => 'ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP',
        'code' => 'SKU123456',
        'info' => 'Some description of the product',
        'price' => 6000,
        'vat' => .0,
    ];

    const P2 = [
        'name' => '',
        'code' => 'SKU234567',
        'info' => 'Another description',
        'price' => 2400,
        'vat' => .0,
    ];

    /** @var \Travelhood\OtpSimplePay\Config */
    public $config;

    /** @var \Travelhood\OtpSimplePay\Service */
    public $simplePay;

    public function SetUp()
    {
        $configArray = require __DIR__ . '/fixture/config.php';
        if(is_file(__DIR__ . '/fixture/config.local.php')) {
            $configArrayLocale = require __DIR__ . '/fixture/config.local.php';
            $configArray = \Travelhood\OtpSimplePay\Util::mergeArray($configArray, $configArrayLocale);
        }
        $this->config = new \Travelhood\OtpSimplePay\Config($configArray);
        $this->config->selectCurrency('HUF');
        $this->simplePay = new \Travelhood\OtpSimplePay\Service($this->config);
    }
}