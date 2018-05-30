<?php

class ProductCollectionTest extends BaseTest
{
    public function testProductCollection()
    {
        $pc = new \Travelhood\OtpSimplePay\ProductCollection();
        $p1 = new \Travelhood\OtpSimplePay\Product(self::P1);
        $p2 = new \Travelhood\OtpSimplePay\Product(self::P2);

        $this->assertEquals(0, count($pc));

        $pc->addProduct($p1);
        $this->assertEquals(1, count($pc));
        $this->assertEquals(0, $pc->key());

        $pc->addProduct($p2);
        $this->assertEquals(2, count($pc));
        $this->assertEquals(1, $pc->key());

        $pc->rewind();
        $this->assertEquals(0, $pc->key());

        $pc->clear();
        $this->assertEquals(0, count($pc));
        $this->assertEquals(0, $pc->key());

        $pc->addProduct($p1, 2);
        $this->assertEquals(1, count($pc));
        $this->assertEquals(0, $pc->key());
        $this->assertEquals(2, $pc->countProduct($p1));
        $this->assertEquals(0, $pc->countProduct($p2));

        $pc->addProduct($p1, 1);
        $this->assertEquals(1, count($pc));
        $this->assertEquals(0, $pc->key());
        $this->assertEquals(3, $pc->countProduct($p1));

        $pc->removeProduct($p1, 1);
        $this->assertEquals(1, count($pc));
        $this->assertEquals(0, $pc->key());
        $this->assertEquals(2, $pc->countProduct($p1));

        $pc->removeProductByCode($p1->getCode());
        $this->assertEquals(0, count($pc));
        $this->assertEquals(0, $pc->key());
        $this->assertEquals(0, $pc->countProduct($p1));
    }
}