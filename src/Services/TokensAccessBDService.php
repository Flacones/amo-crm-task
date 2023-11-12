<?php

namespace App\Services;


use League\OAuth2\Client\Token\AccessTokenInterface;
use SQLite3;

/**
 * Вспомогательный класс для работы с токенами
 * Class TokensAccessBDService
 * @package App\Services
 */
class TokensAccessBDService
{
    /** @var string Название файла базы данных */
    const DB_NAME = 'tokens-data-base.db';


    /**
     * @var \SQLite3
     */
    private SQLite3 $db;

    /**
     * TokensAccessBDService constructor.
     */
    public function __construct()
    {
        $this->db = new SQLite3(self::DB_NAME);
    }

    /**
     * Возвращает Refresh токен
     * @return string
     */
    public function getRefreshTokenFromBD(): string
    {
        $refreshToken = $this->db->query('SELECT refresh_token FROM tokens ORDER BY refresh_token ASC');
        return $refreshToken->fetchArray()['refresh_token'];
    }

    /**
     * Проверка на окончание времени действия token_access (false - токен еще активен, true - токен нужно обновить)
     * @return bool
     */
    public function isTokenExpired(): bool
    {
        $expirationDate = $this->db->query('SELECT expiration_date FROM tokens ORDER BY expiration_date ASC');
        $dateResult = $expirationDate->fetchArray()['expiration_date'];
        if ($dateResult < time()) {
            return true;
        }
        return false;
    }

    /**
     * Проверка на наличие токена в бд
     * @return bool
     */
    public function isTokenExists(): bool
    {
        return $this->db->query('SELECT COUNT(id) as count FROM tokens')->fetchArray()['count'] > 0;
    }

    /**
     * Проверка на наличие таблицы в бд
     * @param string $tableName
     * @return bool
     */
    public function isTableCreated(string $tableName): bool
    {
       return $this->db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$tableName'")->fetchArray() !== false;
    }

    /**
     * Заносит access токен, refresh токен, временную метку окончания работы access токена и тип токена в базу данных
     * @param \League\OAuth2\Client\Token\AccessTokenInterface $token
     */
    public function tokenToBD(AccessTokenInterface $token): void
    {
        $accessToken = $token->getToken();
        $refresh_token = $token->getRefreshToken();
        $expires_date = $token->getExpires();
        $token_type = $token->getValues()['token_type'];
        $db = new SQLite3(self::DB_NAME);
        $db->exec("INSERT INTO tokens (access_token, refresh_token, expiration_date, token_type) VALUES ( '$accessToken','$refresh_token', $expires_date, '$token_type')");
        $db->close();
    }

    /**
     * Возвращает с базы данных access токен, refresh токен, временную метку окончания работы access токена и тип токена
     * @return array
     */
    public function getTokenOptions(): array
    {
        $db = new SQLite3(self::DB_NAME);
        $res = $db->query('SELECT * FROM tokens');

        $row = $res->fetchArray();
        $resultArray = [
            'access_token' => $row['access_token'],
            'refresh_token' => $row['refresh_token'],
            'expires' => $row['expiration_date'],
            'token_type' => $row['token_type'],
        ];
        $db->close();
        return $resultArray;
    }


}
