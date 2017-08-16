<?php
//Основное приложение
//Подключаем основные настройки
const __DIR_INDEX__ = __DIR__;
require_once "Configs/config.php";
require_once "base/Base.php";

//Создаем первый базовый класс - дерево, который будет содержать ссылки на остальные
require_once "base/Registry.php";
$registry = new Registry();

//Подключаем логирование
require_once "base/Logging.php";
$registry->set("log", new Log($registry));
$log = $registry->get("log");
try
{
    //Подключаем модуль ответа
    require_once "base/Response.php";
    $registry->set("response", new Response($registry));
    $log("{MODULE} response loaded");

    require_once "base/Database.php";
    $registry->set("db", new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE));
    $log("{MODULE} DB loaded");

    //Подключаем модуль Парсинга приходящих данных
    require_once "base/Parser.php";
    $registry->set("parser", $__PARSER);
    $log("{MODULE} parser loaded");

    //Модуль конкретного/группы пользователя
    require_once "base/User.php";
    $registry->set("user", new User($registry));
    $log("{MODULE} user loaded");

    require_once "base/Action.php";
    require_once "base/Controller.php";
    $registry->set("controller", new Controller($registry));
    $log("{MODULE} controller loaded");

    $registry->get("response")->SendResponse();
}
//Этот эксепшен - заключительный! Если не было поймано чем-либо еще до этого.
//TODO: Включать дебаг мод только у определенных пользователей.
catch (Exception $exception)
{
    $response = $registry->get("response");
    $response->SetCode(RESPONSE_ERROR_ON_WORK);
    if (DEBUG) $response->SetText($exception->getMessage());
    $response->SendResponse();
}