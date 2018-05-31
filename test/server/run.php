<?php

$devConfig = require __DIR__ . '/../fixture/config.dev.php';
$address = $devConfig['server']['host'].':'.$devConfig['server']['port'];
$command = "php -S {$address} -t ./public";
echo "Firing up server at http://{$address}/", PHP_EOL;
passthru($command);
