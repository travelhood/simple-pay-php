<?php

class HasherTest extends BaseTest
{
    public function provideHmacArray()
    {
        return [
            [
                [
                    'MERCHANT' => 'PUBLICTESTHUF',
                    'REFNOEXT' => '101010514615913074586',
                ],
                '9607a566c832821b8447eea204e6da1e',
            ],
        ];
    }

    /**
     * @dataProvider provideHmacArray
     * @param $array
     * @param $expectedHash
     * @throws \Travelhood\OtpSimplePay\Exception\ConfigException
     */
    public function testArray($array, $expectedHash)
    {
        $this->simplePay->selectCurrency('HUF');
        $hash = $this->simplePay->hasher->hashArray($array);
        $this->assertEquals($expectedHash, $hash);
    }

    public function testFlattenArray()
    {
        $a = [
            'foo' => [
                '1',
                '2',
            ],
            'baz' => 'bax',
        ];
        $f = \Travelhood\OtpSimplePay\Hasher::flattenArray($a);
        $this->assertEquals(['1','2','bax'], $f);
    }
}