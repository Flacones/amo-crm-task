<?php

namespace App\Services;

use League\OAuth2\Client\Token\AccessTokenInterface;
use SQLite3;

/**
 * Вспомогательный класс для работы с бд
 * Class SqliteService
 * @package App\Services
 */
class SqliteService
{
    /** @var string Название файла базы данных */
    const DB_NAME = 'tokens-data-base.db';

    /** @var string Название таблицы токенов */
    const TABLE_NAME = 'tokens';

    /**
     * @var SQLite3
     */
    private SQLite3 $db;

    /**
     * SqliteService constructor.
     */
    public function __construct()
    {
        $this->db = new SQLite3(self::DB_NAME);
    }

    /**
     * SqliteService destructor.
     */
    public function __destruct()
    {
        $this->db->close();
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
     * @return bool
     */
    public function isTableCreated(): bool
    {
        $tableName = self::TABLE_NAME;
        return $this->db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$tableName'")->fetchArray() !== false;
    }

    /**
     * Сохраняет параметры токена в базу данных
     * @param AccessTokenInterface $token
     */
    public function saveTokenToBD(AccessTokenInterface $token): void
    {
        $accessToken = $token->getToken();
        $refresh_token = $token->getRefreshToken();
        $expires_date = $token->getExpires();
        $token_type = $token->getValues()['token_type'];
        $this->db->exec("INSERT INTO tokens (access_token, refresh_token, expiration_date, token_type) VALUES ( '$accessToken','$refresh_token', $expires_date, '$token_type')");
    }

    /**
     * Возвращает с базы данных параметры токена
     * @return array
     */
    public function getTokenOptions(): array
    {
        $res = $this->db->query('SELECT * FROM tokens LIMIT 1');
        $row = $res->fetchArray();

        return [
            'access_token' => $row['access_token'],
            'refresh_token' => $row['refresh_token'],
            'expires' => $row['expiration_date'],
            'token_type' => $row['token_type'],
        ];
    }

    /**
     * Создает таблицу tokens
     * @return void
     */
    public function createTokenTable(): void
    {
        $this->db->exec("CREATE TABLE tokens (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        access_token VARCHAR,
        refresh_token VARCHAR,
        expiration_date INTEGER,
        token_type VARCHAR
         )");
    }

    /**
     * Обновляет параметры токена в базе данных
     * @param AccessTokenInterface $token
     */
    public function updateToken(AccessTokenInterface $token): void
    {
        $accessToken = $token->getToken();
        $refresh_token = $token->getRefreshToken();
        $expires_date = $token->getExpires();
        $token_type = $token->getValues()['token_type'];
        $db = new SQLite3(self::DB_NAME);
        $db->exec("UPDATE tokens SET access_token = '$accessToken', refresh_token = '$refresh_token', expiration_date = $expires_date, token_type = '$token_type' WHERE id = 1");
    }
}
