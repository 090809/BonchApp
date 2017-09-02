<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 09.08.2017
 * Time: 16:34
 */
const USER_GROUP_NONE               = 0;
const USER_GROUP_LOGGED_IN          = 1 << 0;
const USER_GROUP_ABITURIENT         = 1 << 1;
const USER_GROUP_STUDENT            = 1 << 2;
const USER_GROUP_HEAD_STUDENT       = 1 << 3;
const USER_GROUP_WORKER             = 1 << 4;

const USER_GROUP_ADMIN              = 1 << 10;
const USER_GROUP_FULL_ACCESS        = USER_GROUP_NONE | USER_GROUP_ABITURIENT | USER_GROUP_STUDENT | USER_GROUP_HEAD_STUDENT | USER_GROUP_WORKER | USER_GROUP_ADMIN;

const TEN_YEAR = 365 * 24 * 60 * 10 *60;

final class User extends Base
{
    private $group = USER_GROUP_NONE;
    private $hash, $logged_in = false;

    protected function init()
    {
        session_set_cookie_params(TEN_YEAR);
        session_start();

        $this->setGroup(isset($_SESSION['group']) ?? USER_GROUP_NONE);
        $this->hash = isset($_SESSION['hash']) ?? null;
        if (null !== $this->hash)
            $this->logged_in = true;
    }

    public function addGroup($group)
    {
        $this->group |= $group;
        $_SESSION['group'] = $this->group;
    }

    public function removeGroup($group)
    {
        $this->group &= !$group;
        $_SESSION['group'] = $this->group;
    }

    public function setGroup($group)
    {
        $this->group = $group;
        $_SESSION['group'] = $this->group;
    }

    public function inGroup($group)
    {
        return $this->group & $group;
    }

    public function isLoggedIn()
    {
        return $this->logged_in;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
        $_SESSION['hash'] = $hash;
    }
}