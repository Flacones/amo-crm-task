<?php


namespace App\Services;


use League\OAuth2\Client\Token\AccessToken;

/**
 * Вспомогательный класс для работы с токенами
 * Class TokenService
 * @package App\Services
 */
class TokenService
{

    /**
     * @var \App\Services\SqliteService
     */
    private SqliteService $dbService;

    /**
     * TokenService constructor.
     */
    public function __construct()
    {
        $this->dbService = new SqliteService();
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function getToken(): AccessToken
    {
        $tokenParams = $this->dbService->getTokenOptions();
        return new AccessToken($tokenParams);
    }
}