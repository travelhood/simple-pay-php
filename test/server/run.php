<?php

chdir(__DIR__);

$devConfig = require __DIR__ . '/../fixture/config.dev.php';
$address = $devConfig['server']['listen'].':'.$devConfig['server']['port'];
$command = "php -S {$address} -t ./public ../router.php";
echo "Firing up server at http://{$devConfig['server']['domain']}:{$devConfig['server']['port']}/", PHP_EOL;
passthru($command);
