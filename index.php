<?php

//Основное приложение
//Подключаем основные настройки
require_once "Configs/config.php";

//Создаем первый базовый класс - дерево, который будет содержать ссылки на остальные
require_once "base/Base.php";
$base = new Base();

try {
    //Подключаем модуль ответа
    require_once "base/Response.php";
    $base->set("response", new Response($base));

    require_once "base/Database.php";
    $base->set("db", new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE));

    //Подключаем модуль Парсинга приходящих данных
    require_once "base/Parser.php";
    $base->set("parser", $__PARSER);

    //Подключаем логирование
    require_once "base/Logging.php";
    $base->set("log", new Log($base));

    //Оставляем это под вопросом, как и Action. Нужно подумать, как это делать ПРАВИЛЬНО
    require_once "base/Controller.php";
    //$base->set("controller", new Action($base));

    //Модуль конкретного/группы пользователя
    require_once "base/User.php";
    $base->set("user", new User($base));

    require_once "base/Action.php";
} catch (Exception $exception)
{
    $response = $base->get("response");
    $response->SetCode(RESPONSE_ERROR_ON_WORK);
    if (DEBUB) $response->SetText($exception->getMessage());
    $response->SendResponse();
}