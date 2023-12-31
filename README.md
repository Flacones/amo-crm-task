# amo-crm-task

В данном пакете предоставлена реализация тестового задания:
1. Создать страницу и добавить на неё форму из 4-х полей: имя, email, телефон, цена.
2. Заявку из формы сайта создавать на платформе www.amocrm.ru, как сделку с прикрепленным к ней контактом. В контакт передавать имя, email и телефон. В сделку передавать цену
3. Авторизация должна быть выполнена через oauth2.
4. При написании бэкенда придерживаться парадигмы ООП и основных принципов программирования. (Важно показать умение правильно работать с классами)

Для работы библиотеки требуется PHP версии не ниже 7.1.

## Оглавление
- [Установка репозитория GitHub](#Установка-репозитория-GitHub)
- [Установка Библиотеки](#установка-Библиотеки)
- [.env.php](#.env.php)
- [Пошаговый порядок действий](#Пошаговый-порядок-действий)
- [Логика работы приложения](#Логика-работы-приложения)

## Установка репозитория GitHub

```
git clone git@github.com:Flacones/amo-crm-task.git
```
## Установка Библиотеки

Установить библиотеку можно с помощью composer:

```
composer require amocrm/amocrm-api-library
```

## .env.php
Для использования AmoCRMApiClient понадобятся параметры, которые нужно указать в файле .env.php из настроек интеграции. (Пример в файле .env.example.php)
```php
$params = [
    // Идентификатор интеграции в amoCRM
    'CLIENT_ID' => 'xxxxx-xxxxx-xxxxx-xxxxx-xxxxx',
    // Секретный ключ интеграции
    'CLIENT_SECRET' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    // Адрес страницы, на которую будет перенаправлен пользователь после авторизации в amoCRM (должен совпадать с адресом, который указывали в интеграции)
    'REDIRECT_URI' => 'http://test.com/',
    // Поддомен нужного аккаунта
    'BASE_DOMAIN' => 'test'
];
foreach ($params as $key => $value) {
    putenv($key . '=' . $value);
}
```
Доставать данные с .env.php будем с помощью EnvHelper.php
Пример использования
```php
 EnvHelper::getEnvValue('CLIENT_ID');
```
## Пошаговый порядок действий
1. Зайти в свою учетную запись amoCRM
   1. Перейти в AmoМаркет -> "..." -> Создать Интеграцию -> "Внешняя интеграция"
   2. Указываем url куда будет перенаправлять, после авторизации (первая строка)
   3. Выбираем какой предоставить доступ (Предоставить доступ: Все)
   4. Заполняем название интеграции и описание -> Сохраняем
2. После появления интеграции во вкладке "Установленные"
   1. Проваливаемся в интеграцию и во вкладке "Ключи и доступы" берем коды для .env.php
2. На странице index.php
    1. Нажимаем "установить интеграцию" и всплывающем окне выбираем учетную запись, на которой установлена интеграция
    2. После удачной авторизации, закрываем окно авторизации и обновляем index.php
    3. Вносим данные форму и нажимаем "send deal"
   
После этих манипуляций на платформе amocrm.ru в разделе "Сделки" появится отправленная ранее сделка

## Логика работы приложения
![Logic_AP](https://github.com/Flacones/amo-crm-task/blob/main/html/pictures/Logic.drawio.png)