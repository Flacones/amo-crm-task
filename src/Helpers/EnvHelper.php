<?php


namespace App\Helpers;

/**
 * Вспомогательный класс для работы с .env.php
 * Class EnvHelper
 * @package App\Helpers
 */
class EnvHelper
{
    /**
     * Возвращает данные с .env по ключу
     * @param string $keyParam
     * @return string
     */
    public static function getEnvValue(string $keyParam): string
    {
        return getenv($keyParam);
    }
}
