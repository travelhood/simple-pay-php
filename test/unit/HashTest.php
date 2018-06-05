<?php

class HashTest extends BaseTest
{
    public function provideHmacArray()
    {
        return [
            [
                [
                    'MERCHANT' => 'PUBLICTESTHUF',
                    'REFNOEXT' => '101010514615913074586',
                ],
                'FxDa5w314kLlNseq2sKuVwaqZshZT5d6',
                '9607a566c832821b8447eea204e6da1e',
            ],
        ];
    }

    /**
     * @dataProvider provideHmacArray
     * @param array $array
     * @param string $secret
     * @param string $expectedHash
     */
    public function testHmacArray($array, $secret, $expectedHash)
    {
        $hash = \Travelhood\OtpSimplePay\Util::hmacArray($array, $secret);
        $this->assertEquals($expectedHash, $hash);
    }
}