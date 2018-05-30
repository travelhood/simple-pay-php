<?php

class ProductTest extends BaseTest
{
    public function testProductFromArray()
    {
        $p = new \Travelhood\OtpSimplePay\Product(self::P1);
        foreach(self::P1 as $k=>&$v) {
            $this->assertArrayHasKey($k, $p);
            $this->assertEquals($v, $p[$k]);
        }
        $this->assertEquals(self::P1['name'], $p->getName());
        $this->assertEquals(self::P1['code'], $p->getCode());
        $this->assertEquals(self::P1['info'], $p->getInfo());
        $this->assertEquals(self::P1['price'], $p->getPrice());
        $this->assertEquals(self::P1['vat'], $p->getVat());
        $this->assertEquals(self::P1, $p->toArray());
    }

    public function testProductFromArgs()
    {
        $p = new \Travelhood\OtpSimplePay\Product(self::P1['name'], self::P1['code'], self::P1['info'], self::P1['price'], self::P1['vat']);
        $this->assertEquals(self::P1['name'], $p->getName());
        $this->assertEquals(self::P1['code'], $p->getCode());
        $this->assertEquals(self::P1['info'], $p->getInfo());
        $this->assertEquals(self::P1['price'], $p->getPrice());
        $this->assertEquals(self::P1['vat'], $p->getVat());
        $this->assertEquals(self::P1, $p->toArray());
    }
}