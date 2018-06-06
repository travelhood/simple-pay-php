<?php

global $simplePay;
require_once __DIR__ . '/../bootstrap.php';

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP SimplePay PHP API</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css" />
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
include __DIR__ . '/../page'.$_SERVER['PHP_SELF'];
?>
</div>

<br/>

</body>
</html>