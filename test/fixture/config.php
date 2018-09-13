<?php

$devConfig = require __DIR__ . '/config.dev.php';

$port = intval($devConfig['server']['port']);
if($port != 80) {
    $port = ':'.$port;
}
else {
    $port = '';
}

if(is_file(__DIR__ . '/config.merchant.php')) {
    $merchantConfig = require __DIR__ . '/config.merchant.php';
}
else {
    $merchantConfig = [
        'HUF' => [
            'id' => 'PUBLICTESTHUF',
            'secret' => 'FxDa5w314kLlNseq2sKuVwaqZshZT5d6',
        ],
        'EUR' => [
            'id' => 'PUBLICTESTEUR',
            'secret' => '9A2sDc7xh1JKW8r193RwW7X7X2ts837w',
        ],
        'USD' => [
            'id' => 'PUBLICTESTUSD',
            'secret' => 'Aa9cDbHc1i2lLmN4z3C542zjXqZiDiCj',
        ],
    ];
}

return [
    'curl' => true,
    'live' => false,
    'timeout' => 60,
    'log' => [
        'level' => 'debug',
        'path' => __DIR__ . '/../log/simplepay.log',
    ],
    'url' => [
        'back' => 'http://'.$devConfig['server']['domain'].$port.'/back.php',
        'timeout' => 'http://'.$devConfig['server']['domain'].$port.'/timeout.php',
    ],
    'merchant' => $merchantConfig,
];
