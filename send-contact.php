<?php

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
}
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/.env.php';
}

use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use App\Services\LeadModelService;
use App\Helpers\EnvHelper;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Client\AmoCRMApiClient;
use App\Services\SqliteService;
use App\Services\TokenService;

try {
    $sqliteService = new SqliteService();
    $tokenService = new TokenService();
    $apiClient = new AmoCRMApiClient(
        EnvHelper::getEnvValue('CLIENT_ID'),
        EnvHelper::getEnvValue('CLIENT_SECRET'),
        EnvHelper::getEnvValue('REDIRECT_URI')
    );
    $apiClient->getOAuthClient()->setBaseDomain(EnvHelper::getEnvValue('BASE_DOMAIN'));
    $apiClient->setAccountBaseDomain(EnvHelper::getEnvValue('BASE_DOMAIN'));
    if (!$sqliteService->isTableCreated()) {
        $sqliteService->createTokenTable();
    }

    if ($sqliteService->isTokenExists()) {
        $accessToken = $tokenService->getToken();
    } else {
        //TODO: Придумать, как передать codeAccess
        $codeAccess = 'def5020037def02363362a4205a70bc82ff7f5d0542d757eefe5408f455f6c37260b93a5dfb73abcc7778668fd007a064192b30c3c719095a8ef587d57598f6b6005fe2809d25f81274def8d0cbfceac778bc2248da73eddb314cc1543f8cc40cf67cb5280057b9681b83a0bf8bc6a1850389d0dcdde8cbb2c81705121e731d97312bb82e5b77a5ba037e2c8a9db9c8f384614181aead2b256b7c858f0de2d976bfe622089d068970f014ee5281af6a02279d7048094210a0522d76084a406fc458f11b194a0044b6522e07084e7d833b3ce9d985e330cde28b466e6438d1a1e4fb55cc0619907d4d0481f59d8ebb43849a1ef1e23f7a678a667d25bcbbcef7b29b5c0908f1fda08b04db783cc5dbc54ae24f675ac5f92c5288bc5aecc68803ef8494de96fccee6c2803f8baeab4ef1f123a4dc8759c16f7c15eddb0c0d7b11343aeb89da0445bd9ce5dc9a902531702de3f03addbe39e2d52d739e8745d0c3304dc07da6e591dac102815739025c71ca04edb95c3750ae2d6e59b1e12cf0ad2a8009f2aa843b095cbf172a688ac58a1ac7167d4830c3350145d0c0c4f1e6a9e3eb01e5f0e4d0cfa92c1a79a94be8013e8a1bd30de846f7c8d4aa966d9b186cbcef3ec9f1526e6e757a71667ddbc452725bccf1a2aa1b0f0c3289a2519a343f996798c6e152359b4c3';
        if (!$codeAccess) {
            throw new Exception('Отсутствует секретный код авторизации');
        }
        $accessToken = $apiClient->getOAuthClient()->setBaseDomain('falconchik2517.amocrm.ru')->getAccessTokenByCode($codeAccess);
        $sqliteService->saveTokenToBD($accessToken);
    }
    if ($accessToken->hasExpired()) {
        $newToken = $apiClient->getOAuthClient()->getAccessTokenByRefreshToken($accessToken);
        $sqliteService->updateToken($newToken);
        $accessToken = $newToken;
    }
    $apiClient->setAccessToken($accessToken);

    // Получение сервиса по сделкам с апи клиента
    $leadsService = $apiClient->leads();

    //Отправить сделку
    $leadModelService = new LeadModelService();
    $res = $leadsService->addOneComplex($leadModelService->getLeadModel($_POST));

    if ($res->getId() > 0) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/success-page.php';
    } else {
        throw new Exception('Не удалось создать сделку');
    }
} catch (AmoCRMoAuthApiException | AmoCRMMissedTokenException | Throwable $e) {
    $errorMessage = $e->getMessage();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/error-page.php';
}

