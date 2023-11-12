<?php

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
}
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/.env.php';
}

use App\Services\LeadModelService;
use App\Helpers\EnvHelper;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\OAuth2\Client\Provider\AmoCRM;
use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;


try {
    $db = new SQLite3('tokens-data-base.db');
    $tokenHelper = new \App\Services\TokensAccessBDService();
    $apiClient = new AmoCRMApiClient(
        EnvHelper::getEnvValue('CLIENT_ID'),
        EnvHelper::getEnvValue('CLIENT_SECRET'),
        EnvHelper::getEnvValue('REDIRECT_URI')
    );
    if ($tokenHelper->isTableCreated('tokens')) {
        if ($tokenHelper->isTokenExists()) {
            $accessToken = new AccessToken($tokenHelper->getTokenOptions());
            $db->close();
        } else {
            $accessToken = $apiClient->getOAuthClient()->setBaseDomain('falconchik2517.amocrm.ru')->getAccessTokenByRefreshToken($accessToken = new AccessToken($tokenHelper->getTokenOptions()));
        }
    } else {
        //бд нет
        $codeAccess = 'def5020010afcdf95f8fbe5ec2baffa4a0a0e4a9d73a35a7e94e73af37273293186074221dee947633f412788ba3613ea28d4e9a9cd2e5d49a271b9b7a12e9d673ef5427ce46a7666f0318c06ac893ebe97323609bbc954540949d357e77b4e154e032e8f1342655fb5d0a51c062cd3e24c8807321028de710a57c76d31a1ab223129279c35e25a8c6574b0b6e3235ae056430110e659ee040962a99d48a27fae56e486daa02475ac91748431686bdc7dc8a917c70785cdd5acd766ca118a8f8b7baae998252ddff782a803a3049b04b6921638e2d9e1fcd39e47a938f416a6118c6ca15c3934cbde80da272fe76b79b295c451063d195f84aa5206e84cfffba3b356d7a0b44af8e4100723d3d773efc0a951d4fe432c2742e5fd570c47df5623ad09cc2be5372366351bb1d8fe2cad4ebfb7c0cdabe0d529aec62b04af12e45b0f6fb01913f8ee8672626944903013e8390c3229c3f61fe9ce86c66f1a06ea892fed7c65075bb2aac7a3804f92a2e7effea629339bd6a186bcf6a8b1a84afcfcebcc1215cefe7c810e9d1070e9e20ef67a32f33ad0fa258a1f4188e465c19f99b69a5ed6e83789091615e4d727e347cf4cfcdaa4e2ef18c46e61e18aaa916d3bb9e38a2b90632775da611302400615ecdb647fa3d0d2f10f224a346f19d3cc7c234bbf9912bdcc18a';
        $accessToken = $apiClient->getOAuthClient()->setBaseDomain('falconchik2517.amocrm.ru')->getAccessTokenByCode($codeAccess);
        $db->exec("CREATE TABLE IF NOT EXISTS tokens (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        access_token VARCHAR,
        refresh_token VARCHAR,
        expiration_date INTEGER,
        token_type VARCHAR
         )");
        $db->close();
        $tokenHelper = new \App\Services\TokensAccessBDService();
        $tokenHelper->tokenToBD($accessToken);
        $accessToken = new AccessToken($tokenHelper->getTokenOptions());
        $db->close();
    }
    $apiClient->setAccessToken($accessToken)
        ->setAccountBaseDomain(EnvHelper::getEnvValue('BASE_DOMAIN'))
        ->onAccessTokenRefresh(
            function (AccessTokenInterface $accessToken, string $baseDomain) {
                saveToken(
                    [
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $baseDomain,
                    ]
                );
            });
    // Получение сервиса по сделкам с апи клиента
    $leadsService = $apiClient->leads();
    //Отправить сделку
    $res = $leadsService->addOneComplex(LeadModelService::getLeadModel($_POST));

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/success-page.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/success-page.php';
    }
    echo '<pre>';
    print_r('normal');
    print_r($res);
    echo '</pre>';


} catch (AmoCRMoAuthApiException $e) {
    echo '<pre>';
    print_r($e);
    echo '</pre>';
} catch (\AmoCRM\Exceptions\AmoCRMMissedTokenException $e) {
    echo '<pre>';
    print_r($e);
    echo '</pre>';
} catch (Throwable $e) {
    echo '<pre>';
    print_r($e);
    echo '</pre>';
}

