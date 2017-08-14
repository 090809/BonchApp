<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 09.08.2017
 * Time: 16:34
 */
const TEN_YEAR = 10*365*24*60*60;
final class User
{
    private $base;
    public function __construct($base)
    {
        $this->base = $base;
        session_set_cookie_params(TEN_YEAR);
        session_start();
    }

    public function __get($name)
    {
        return $this->base->get($name);
    }

    public function __set($name, $value)
    {
        $this->base->set($name, $value);
    }

}