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

const USER_NONE_1                   = 1 << 5;
const USER_NONE_2                   = 1 << 6;
const USER_NONE_3                   = 1 << 7;
const USER_NONE_4                   = 1 << 8;
const USER_NONE_5                   = 1 << 9;

const USER_GROUP_ADMIN              = 1 << 10;
const USER_GROUP_FULL_ACCESS        = USER_GROUP_ABITURIENT | USER_GROUP_STUDENT | USER_GROUP_HEAD_STUDENT | USER_GROUP_WORKER | USER_GROUP_ADMIN;

const TEN_YEAR = 365 * 24 * 60 * 10 *60;

final class User extends Base
{
    private $group = USER_GROUP_NONE;
    private $hash, $logged_in = false;
    private $data = array();

    protected function init()
    {
        foreach ($_SESSION as $name => $value)
        {
            switch ($name)
            {
                case 'group':
                    $this->setGroup($value);
                    break;
                case 'hash':
                    $this->hash = $value;
                    break;
                default:
                    $this->set($name, $value);
                    break;
            }
        }

        if ($this->hash !== null)
            $this->logged_in = true;
    }

    /**
     * Description: this function add group to current User.
     * @param $group
     */
    public function addGroup($group)
    {
        $this->group |= $group;
        $_SESSION['group'] = $this->group;
    }

    /**
     * @param $group
     */
    public function removeGroup($group)
    {
        $this->group &= !$group;
        $_SESSION['group'] = $this->group;
    }

    /**
     * Description: this function setting up group to current user and save it to session
     * @param $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
        $_SESSION['group'] = $this->group;
    }

    /**
     * @param $group
     * @return int
     */
    public function inGroup($group) : int
    {
        return $this->group & $group;
    }

    /**
     * @return bool
     */
    public function isLoggedIn() : bool
    {
        return $this->logged_in;
    }

    /**
     * @param $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        $_SESSION['hash'] = $hash;
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->data[$name]  = $value;
        $_SESSION[$name]    = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->data[$name];
    }

    public function hasPermission($file, $func = 'index') : bool
    {
        $temp = explode('/', $file);
        $class = $temp[count($temp) - 1];

        return $this->inGroup($this->db->query("   SELECT permission
                                                FROM user_group_permission 
                                                WHERE file = '$file' 
                                                AND class = '$class' 
                                                AND func = '$func'
                                                LIMIT 0, 1")->row['permission']);
    }
}