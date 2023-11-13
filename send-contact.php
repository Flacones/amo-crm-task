<?php

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
}
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/.env.php';
}

use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use App\Services\AuthCodeService;
use App\Services\LeadModelService;
use App\Helpers\EnvHelper;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Client\AmoCRMApiClient;
use App\Services\SqliteService;
use App\Services\TokenService;

try {
    $authCodeService = new AuthCodeService();
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
        $codeAccess = $authCodeService->getAuthCode();
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

