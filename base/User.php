<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 09.08.2017
 * Time: 16:34
 */
const TEN_YEAR = 10*365*24*60*60;
final class User extends Base
{
    function init()
    {
        session_set_cookie_params(TEN_YEAR);
        session_start();
    }
}