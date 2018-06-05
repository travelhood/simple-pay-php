<?php

$devConfig = require __DIR__ . '/config.dev.php';

return [
    'curl' => true,
    'live' => false,
    'timeout' => 60,
    'url' => [
        'back' => 'http://'.$devConfig['server']['host'].':'.$devConfig['server']['port'].'/back.php',
        'timeout' => 'http://'.$devConfig['server']['host'].':'.$devConfig['server']['port'].'/timeout.php',
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
