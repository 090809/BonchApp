<?php
//Основное приложение
//Подключаем основные настройки
const __DIR_INDEX__ = __DIR__;
require_once "Configs/config.php";

//Создаем первый базовый класс - дерево, который будет содержать ссылки на остальные
require_once "base/Base.php";
$base = new Base();

//Подключаем логирование
require_once "base/Logging.php";
$base->set("log", new Log($base));
$log = $base->get("log");
try
{
    //Подключаем модуль ответа
    require_once "base/Response.php";
    $base->set("response", new Response($base));
    $log("{MODULE} response loaded");

    require_once "base/Database.php";
    $base->set("db", new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE));
    $log("{MODULE} DB loaded");

    //Подключаем модуль Парсинга приходящих данных
    require_once "base/Parser.php";
    $base->set("parser", $__PARSER);
    $log("{MODULE} parser loaded");

    //Модуль конкретного/группы пользователя
    require_once "base/User.php";
    $base->set("user", new User($base));
    $log("{MODULE} user loaded");

    require_once "base/Action.php";
    require_once "base/Controller.php";
    $base->set("controller", new Controller($base));
    $log("{MODULE} controller loaded");


    $base->get("response")->SendResponse();
}
//Этот эксепшен - заключительный! Если не было поймано чем-либо еще до этого.
//TODO: Включать дебаг мод только у определенных пользователей.
catch (Exception $exception)
{
    $response = $base->get("response");
    $response->SetCode(RESPONSE_ERROR_ON_WORK);
    if (DEBUG) $response->SetText($exception->getMessage());
    $response->SendResponse();
}