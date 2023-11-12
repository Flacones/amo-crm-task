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
     * @var SqliteService
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
     * @return AccessToken
     */
    public function getToken(): AccessToken
    {
        $tokenParams = $this->dbService->getTokenOptions();
        return new AccessToken($tokenParams);
    }
}