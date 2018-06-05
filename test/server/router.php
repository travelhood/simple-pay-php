<?php

if (file_exists(__DIR__ . '/public/' . $_SERVER['REQUEST_URI'])) {
    return false;
}

if (file_exists(__DIR__ . '/page' . $_SERVER['PHP_SELF'])) {
    include __DIR__ . '/public/index.php';
    return true;
}

return false;