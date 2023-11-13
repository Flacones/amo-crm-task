<?php

use AmoCRM\Client\AmoCRMApiClient;
use App\Helpers\EnvHelper;
use App\Services\AuthCodeService;

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
}
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/.env.php';
}

$authCodeService = new AuthCodeService();
$apiClient = new AmoCRMApiClient(
    EnvHelper::getEnvValue('CLIENT_ID'),
    EnvHelper::getEnvValue('CLIENT_SECRET'),
    EnvHelper::getEnvValue('REDIRECT_URI')
);

if (isset($_GET['code'])) {
    $authCodeService->saveAuthCode($_GET['code']);
    require_once $_SERVER['DOCUMENT_ROOT'] . '/save-code.php';
    die();
}

$button = null;
if (!$authCodeService->isAuthCodeExists()) {
    $state = bin2hex(random_bytes(16));
    $button = $apiClient->getOAuthClient()->getOAuthButton(
        [
            'title' => 'Установить интеграцию',
            'compact' => false,
            'class_name' => 'className',
            'color' => 'default',
            'error_callback' => 'handleOauthError',
            'state' => $state,
        ]
    );
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/style.css">
    <title>amoSrm task</title>
</head>
<body>
<div id="container">
    <h1>&bull; amo SRM task &bull;</h1>
    <?php
    if ($button) {
        echo $button;
    } else {
        ?>
    <div class="underline">
    </div>
        <form action="send-contact.php" method="post" id="contact_form">
            <div class="name">
                <label for="name"></label>
                <input type="text" placeholder="name" name="name" required id="name">
            </div>
            <div class="email">
                <label for="email"></label>
                <input type="email" placeholder="e-mail" name="email" required id="email">
            </div>
            <div class="telephone">
                <label for="phone"></label>
                <input type="text" placeholder="phone" name="phone" required id="phone">
            </div>
            <div class="price">
                <label for="price"></label>
                <input type="text" placeholder="price" name="price" required id="price">
            </div>
            <div class="submit">
                <input type="submit" value="Send Deal" id="form_button"/>
            </div>
        </form>
        <?php
    }
    ?>
</div>
</body>
</html>