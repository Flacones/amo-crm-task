<?php


namespace App\Services;


/**
 * Вспомогательный класс для работы с кодом авторизации
 * Class AuthCodeService
 * @package App\Services
 */
class AuthCodeService
{
    /**
     * Название документа с кодом авторизации
     */
    const FILE_NAME = 'auth_code.txt';

    /**
     * Сохранение кода авторизации в документ
     * @param string $authCode
     */
    public function saveAuthCode(string $authCode): void
    {
        file_put_contents(self::FILE_NAME, $authCode);
    }

    /**
     * Проверка на существование документа и на наличие кода авторизации в нем
     * @return bool
     */
    public function isAuthCodeExists(): bool
    {
        if (!file_exists(self::FILE_NAME)) {
            return false;
        }
        $data = file_get_contents(self::FILE_NAME);
        return $data !== false && strlen($data) > 0;
    }

    /**
     * Возвращает код авторизации
     * @return string
     */
    public function getAuthCode(): string
    {
        return file_get_contents(self::FILE_NAME);
    }
}