<?php

return [
    // Идентификатор интеграции в amoCRM
    'CLIENT_ID' => 'xxxxx-xxxxx-xxxxx-xxxxx-xxxxx',
    //Cекретный ключ интеграции
    'CLIENT_SECRET' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    //Адрес страницы, на которую будет перенаправлен пользователь после авторизации в amoCRM (должен совпадать с адресом, котороый указывали в интеграции)
    'REDIRECT_URI' => 'http://test.com/',
    // Поддомен нужного аккаунта
    'BASE_DOMAIN' => 'test'
];
