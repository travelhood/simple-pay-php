<?php

class ConfigTest extends BaseTest
{
    public function provideArrayObject()
    {
        return [
            [['foo'=>'bar', 'baz'=>'bax'], new class { public $foo = 'bar'; public $baz = 'bax'; }],
        ];
    }

    /**
     * @dataProvider provideArrayObject
     * @param $array
     * @param $object
     */
    public function testArrayObject($array, $object)
    {
        $config = new \Travelhood\OtpSimplePay\Config($array);
        foreach($array as $k=>$v) {
            $this->assertObjectHasAttribute($k, $object);
            $this->assertEquals($v, $config->$k);
        }
        foreach($object as $k=>$v) {
            $this->assertArrayHasKey($k, $array);
            $this->assertEquals($v, $config->$k);
        }
        foreach($config as $k=>$v) {
            $this->assertObjectHasAttribute($k, $object);
            $this->assertEquals($v, $object->$k);
            $this->assertArrayHasKey($k, $array);
            $this->assertEquals($v, $array[$k]);
        }
    }
}