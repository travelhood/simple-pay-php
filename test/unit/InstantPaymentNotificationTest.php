<?php

class InstantPaymentNotificationTest extends BaseTest
{
    public function SetUp()
    {
        parent::SetUp();
        parse_str(file_get_contents(__DIR__ . '/../fixture/ipn-success.raw'), $_POST);
    }

    public function testInstantPaymentNotification()
    {
        $page = $this->simplePay->pagePaymentNotification();
        $page->setDate('2018-06-04 11:07:44');
        $this->assertArrayHasKey('HASH', $page);
        $response = $page->getMessage();
        $message = file_get_contents(__DIR__ . '/../fixture/ipn-success.hash');
        $this->assertEquals($message, $response);
    }
}