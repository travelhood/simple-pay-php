<?php

class UtilTest extends BaseTest
{
    const KEY = '<my-secret-key>';

    public function provideHmac()
    {
        return [
            [self::KEY, 'data1', '0c132a8633031fa2de7bc774d84412da'],
            [self::KEY, 'data2', '42a6c345e1d1a5704cc094547c9c6b9d'],
        ];
    }

    /**
     * @dataProvider provideHmac
     * @param $key
     * @param $data
     * @param $expected
     */
    public function testHmac($key, $data, $expected)
    {
        $hash = \Travelhood\OtpSimplePay\Util::hmac($data, $key);
        $this->assertEquals($expected, $hash);
    }

    public function testHmacDataException()
    {
        $this->expectException(InvalidArgumentException::class);
        \Travelhood\OtpSimplePay\Util::hmac('','');
    }

    public function testHmacKeyException()
    {
        $this->expectException(InvalidArgumentException::class);
        \Travelhood\OtpSimplePay\Util::hmac('data','');
    }


    public function provideHmacArray()
    {
        return [
            [self::KEY, ['foo'=>'bar'], '063cbab0e7d6e011ea45a781c3fb9918'],
        ];
    }

    /**
     * @dataProvider provideHmacArray
     * @param $key
     * @param $array
     * @param $expected
     */
    public function testHmacArray($key, $array, $expected)
    {
        $hash = \Travelhood\OtpSimplePay\Util::hmacArray($array, $key);
        $this->assertEquals($expected, $hash);
    }
}