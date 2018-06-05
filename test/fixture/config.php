<?php

$devConfig = require __DIR__ . '/config.dev.php';

$port = intval($devConfig['server']['port']);
if($port != 80) {
    $port = ':'.$port;
}
else {
    $port = '';
}

return [
    'curl' => true,
    'live' => false,
    'timeout' => 60,
    'url' => [
        'back' => 'http://'.$devConfig['server']['domain'].$port.'/back.php',
        'timeout' => 'http://'.$devConfig['server']['domain'].$port.'/timeout.php',
    ],
    'merchant' => [
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
    ],
];
