<?php

class ConfigTest extends BaseTest
{
    public function provideConfig()
    {
        return [
            [
                [
                    'merchant' => [
                        'HUF' => [
                            'id' => 'id',
                            'secret' => 'secret',
                        ]
                    ],
                ]
            ],
            [ require __DIR__ . '/../fixture/config.php' ],
        ];
    }

    /**
     * @dataProvider provideConfig
     * @param $array
     * @throws \Travelhood\OtpSimplePay\Exception\ConfigException
     */
    public function testConfig($array)
    {
        $config = new \Travelhood\OtpSimplePay\Config($array);
        $this->assertEquals('HUF', $config->getCurrency());
        $this->assertArrayHasKey('timeout', $config);
        $this->assertArrayHasKey('merchant', $config);
        $this->assertArrayHasKey('HUF', $config['merchant']);
        $this->assertArrayHasKey('id', $config['merchant']['HUF']);
        $this->assertArrayHasKey('secret', $config['merchant']['HUF']);
        $this->assertGreaterThan(0, strlen($config['merchant']['HUF']['id']));
        $this->assertGreaterThan(0, strlen($config['merchant']['HUF']['secret']));
    }

    public function testSelectCurrency()
    {
        $config = new \Travelhood\OtpSimplePay\Config([
            'merchant' => [
                'HUF' => ['id'=>'idhuf', 'secret'=>'secrethuf'],
                'EUR' => ['id'=>'ideur', 'secret'=>'secreteur'],
            ],
        ]);
        $this->assertEquals('HUF', $config->getCurrency());

        $config = new \Travelhood\OtpSimplePay\Config([
            'merchant' => [
                'EUR' => ['id'=>'ideur', 'secret'=>'secreteur'],
                'HUF' => ['id'=>'idhuf', 'secret'=>'secrethuf'],
            ],
        ]);
        $this->assertEquals('EUR', $config->getCurrency());
        $this->assertEquals('ideur', $config['merchant_id']);
        $this->assertEquals('secreteur', $config['merchant_secret']);

        $config->selectCurrency('HUF');
        $this->assertEquals('HUF', $config->getCurrency());
        $this->assertEquals('idhuf', $config['merchant_id']);
        $this->assertEquals('secrethuf', $config['merchant_secret']);
    }

    public function testReadonlyFields()
    {
        $config = new \Travelhood\OtpSimplePay\Config([
            'merchant' => [
                'HUF' => ['id'=>'idhuf', 'secret'=>'secrethuf'],
                'EUR' => ['id'=>'ideur', 'secret'=>'secreteur'],
            ],
        ]);
        $this->expectException(\Travelhood\OtpSimplePay\Exception\ConfigException::class);
        $this->expectExceptionMessageRegExp("/^Cannot set read-only key: /");
        $config['merchant_id'] = 'test';
    }

    public function testInvalidCurrency()
    {
        $this->expectException(\Travelhood\OtpSimplePay\Exception\ConfigException::class);
        $this->expectExceptionMessageRegExp("/^Invalid currency: /");
        $config = new \Travelhood\OtpSimplePay\Config([
            'merchant' => [
                'HFF' => ['id'=>'id', 'secret'=>'secret'],
            ],
        ]);
    }

    public function testSelectInvalidCurrency()
    {
        $this->expectException(\Travelhood\OtpSimplePay\Exception\ConfigException::class);
        $this->expectExceptionMessageRegExp("/^Invalid currency: /");
        $config = new \Travelhood\OtpSimplePay\Config([
            'merchant' => [
                'EUR' => ['id'=>'id', 'secret'=>'secret'],
                'HUF' => ['id'=>'id', 'secret'=>'secret'],
            ],
        ]);
        $config->selectCurrency('_');
    }

    public function testInvalidMerchant()
    {
        $this->expectException(\Travelhood\OtpSimplePay\Exception\ConfigException::class);
        $this->expectExceptionMessage("Invalid value for merchant");
        $config = new \Travelhood\OtpSimplePay\Config([
            'merchant' => 'HUF_123',
        ]);
    }

    public function testNonArrayMerchant()
    {
        $this->expectException(\Travelhood\OtpSimplePay\Exception\ConfigException::class);
        $this->expectExceptionMessageRegExp("/^Value for [A-Z]{3} merchant must be an array$/");
        $config = new \Travelhood\OtpSimplePay\Config([
            'merchant' => [
                'HUF' => 'not array',
            ],
        ]);
    }

    public function testMissingMerchantKey()
    {
        $this->expectException(\Travelhood\OtpSimplePay\Exception\ConfigException::class);
        $this->expectExceptionMessageRegExp("/^Missing key [^\s]+ for [A-Z]{3} merchant$/");
        $config = new \Travelhood\OtpSimplePay\Config([
            'merchant' => [
                'HUF' => [],
            ],
        ]);
    }

    public function testInvalidMerchantKey()
    {
        $this->expectException(\Travelhood\OtpSimplePay\Exception\ConfigException::class);
        $this->expectExceptionMessageRegExp("/^Invalid [^\s]+ value for [A-Z]{3} merchant$/");
        $config = new \Travelhood\OtpSimplePay\Config([
            'merchant' => [
                'HUF' => ['id'=>false],
            ],
        ]);
    }
}