<?php

namespace Travelhood\OtpSimplePay\Request;

use Travelhood\OtpSimplePay\Request;

class Curl extends Request
{
    public function fetch()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_url);
        if($this->_method == self::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->_query));
        }
        else {
            curl_setopt($ch, CURLOPT_POST, false);
            $this->_url.= '?'.http_build_query($this->_query);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $raw = curl_exec($ch);
        curl_close($ch);
        return $this->parse($raw);
    }
}