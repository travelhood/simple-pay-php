<?php

class InstantOrderStatusTest extends BaseTest
{
    public function provideOrderRefs()
    {
        return [
            ['101010514615913074586', 'HUF'],
        ];
    }

    /**
     * @dataProvider provideOrderRefs
     * @param string $orderRef
     * @param string $currency
     */
    public function testInstantOrderStatus($orderRef, $currency)
    {
        $status = $this->simplePay->instantOrderStatus($orderRef, $currency);
        $this->assertNotEquals('INVALID HASH', $status->getOrderStatus());
        $this->assertRegExp('/^[\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2}:[\d]{2}$/', $status->getOrderDate());
    }
}