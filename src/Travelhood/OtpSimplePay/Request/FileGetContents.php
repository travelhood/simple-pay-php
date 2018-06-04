<?php

namespace Travelhood\OtpSimplePay\Request;

use Travelhood\OtpSimplePay\Request;

class FileGetContents extends Request
{
    public function fetch()
    {
        $options = array(
            'http' => array(
                'method' => $this->_method,
                'header' => "Accept-language: en\r\n". "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($this->_query, '', '&'),
            ),
        );
        $context = stream_context_create($options);
        $raw = file_get_contents($this->_url, true, $context);
        return $this->parse($raw);
    }
}