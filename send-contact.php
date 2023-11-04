<?php

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
}

use AmoCRM\OAuth2\Client\Provider\AmoCRM;


$object = new AmoCRM();
