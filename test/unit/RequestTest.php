<?php

class RequestTest extends BaseTest
{
    public function testCreateRequest()
    {
        $this->simplePay->config['curl'] = true;
        $r = $this->simplePay->createRequest('http://localhost/');
        $this->assertInstanceOf(\Travelhood\OtpSimplePay\Request\Curl::class, $r);

        $this->simplePay->config['curl'] = false;
        $r = $this->simplePay->createRequest('http://localhost/');
        $this->assertInstanceOf(\Travelhood\OtpSimplePay\Request\FileGetContents::class, $r);
    }
}