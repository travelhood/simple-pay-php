<?php

global $simplePay;
require_once __DIR__ . '/../bootstrap.php';

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP SimplePay PHP API</title>
    <link rel="stylesheet" href="https://npmcdn.com/bulma@0.7.1/css/bulma.min.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
</head>
<body>
<nav class="navbar has-shadow is-spaced">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item" href="https://simplepay.hu/" target="_blank">
                <img src="/img/simplepay_logo.png" />
            </a>
        </div>
    </div>
</nav>

<br/>

<div class="container">
<?php
$path = __DIR__ . '/../page'.$_SERVER['PHP_SELF'];
if(!is_file($path)) {
?>
    <div class="notification is-warning">
        <h2>404</h2>
        <h4>No such page!</h4>
    </div>
<?php
} else {
    try {
        include $path;
    }
    catch(\Throwable $e) { ?>
        <div class="notification is-danger">
            <h2>Error!</h2>
            <h4><?= $e->getMessage() ?></h4>
            <pre><?= $e->getTraceAsString() ?></pre>
        </div>
    <?php
    }
}
?>
</div>

<br/>

</body>
</html>