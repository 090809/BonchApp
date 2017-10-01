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

final class User extends Base
{
    //Это не студенческая группа, а группа доступа.
    private $perm_group = USER_GROUP_NONE;
    private $hash, $logged_in = false;
    private $data = array();
    private $id;

    protected function init()
    {
        foreach ($_SESSION as $name => $value)
        {
            switch ($name)
            {
                case 'id':
                    $this->id = $value;
                    break;
                case 'perm_group':
                    $this->setPermGroup($value);
                    break;
                case 'hash':
                    $this->hash = $value;
                    break;
                default:
                    $this->set($name, $value);
                    break;
            }
        }

        $ud_hash = $this->calculateUserDeviceHash();
        if ($this->hash !== null && $ud_hash === $this->get('ud_hash'))
            $this->logged_in = true;
        else
            $this->reset();
        $this->log->logging(['info' => $this->getOrUpdateInfoAboutUser()]);
    }

    /**
     * Description: this function add group to current User.
     * @param $group
     */
    public function addPermGroup($group)
    {
        $this->perm_group |= $group;
        $_SESSION['perm_group'] = $this->perm_group;
    }

    /**
     * @param $group
     */
    public function removePermGroup($group)
    {
        $this->perm_group &= !$group;
        $_SESSION['perm_group'] = $this->perm_group;
    }

    /**
     * Description: this function setting up group to current user and save it to session
     * @param $group
     */
    public function setPermGroup($group)
    {
        $this->perm_group = $group;
        $_SESSION['perm_group'] = $this->perm_group;

        if ($group & USER_GROUP_ADMIN)
            $this->set('allow_debug', true);
    }

    /**
     * @param $group
     * @return int
     */
    public function inPermGroup($group) : int
    {
        return $this->perm_group & $group;
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
        if (isset($this->data[$name]))
            return $this->data[$name];
        return null;
    }

    public function __get($name)
    {
        return $this->get($name) ?? parent::__get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param string $file
     * @param string $func
     * @return bool
     */
    public function hasPermission($file, $func = 'index') : bool
    {
        $temp = explode('/', $file);
        $class = $temp[count($temp) - 1];
        $query = $this->db->query("         SELECT permission
                                                FROM user_group_permission 
                                                WHERE `file` = '$file' 
                                                AND `class` = '$class' 
                                                AND `func` = '$func'
                                                LIMIT 0, 1");

        if ($query->num_rows > 0) {
            if ($this->inPermGroup($query->row['permission']))
            {
                return true;
            }
            if ($this->isLoggedIn()) {
                return $this->db->query(" SELECT 1 as allowed
                                                FROM user_permissions
                                                WHERE `file` = '$file'
                                                AND `class` = '$class'
                                                AND `func` = '$func'
                                                AND `user_id` = " . $this->id . ' LIMIT 0, 1')->num_rows > 0;
            }
            return false;
        }
        return true;
    }

    public function getOrUpdateInfoAboutUser($forceUpdate = false) : array
    {
        if ($forceUpdate || $this->get('study_group_name') === null)
        {
            $id = $this->id;
            $array = $this->getInfoAboutUser($id);
            if (!count($array)) {
                //Беда печаль пришла нежданно-негадано, нужно прогрузить данные с курла

                // @TODO: КУРЛ ФОР ЭВРЕБАДИ ДЕНЦ
                //$this->controller->load('curl/curl');
                //$array = json_decode($this->controller_curl->Send(BONCH_USER_INFO, $this->user->id));
                {
                    $array = array();
                    $array['id'] = $id;
                    $array['study_group_id'] = array('1');
                    $array['primary_study_group'] = 1;
                    $array['first_name'] = 'test_' . $id;
                    $array['last_name'] = 'test_' . $id;
                    $array['middle_name'] = 'test_' . $id;
                    $array['birthday'] = '1970-01-01';
                }

                if ($array)
                {
                    $this->db->query("INSERT INTO `user_info` (`id`, `first_name`, `last_name`, `middle_name`, `birthday`)   
                                                          VALUES ('$array[id]', '$array[first_name]', '$array[last_name]', '$array[middle_name]', '$array[birthday]')");
                    if (is_array($array['study_group_id']))
                        foreach ($array['study_group_id'] as $value)
                            $this->db->query("INSERT INTO `user_academical_groups` VALUES ('$array[id]', '$value', 0)");

                    $this->db->query("UPDATE `user_academical_groups` SET `primary` = 1 WHERE user_id = $id AND academical_group_id = $array[primary_study_group]");

                }
                /*else return array(
                    'perm_group' => null,
                    'study_group_id' => null,
                    'study_group_name' => '',
                    'first_name' => '',
                    'last_name' => '',
                    'birthday' => null,
                );*/
            }

            foreach ($array as $key => $value)
                $this->set($key, $value);
        }

        return array(
            'perm_group' => $this->perm_group,
            'study_group_id' => $this->get('study_group_id'),
            'study_group_name' => $this->get('study_group_name'),
            'first_name' => $this->get('first_name'),
            'last_name' => $this->get('last_name'),
            'birthday' => $this->get('birthday'),
        );
    }

    public function getInfoAboutUser($id = null)
    {
        if ($id === null)
            $id = $this->id;
        return $this->db->query("
                  SELECT   (SELECT academical_group_id FROM user_academical_groups WHERE `primary` = 1 AND user_id = `user_info`.id) as `study_group_id`,
                           (SELECT study_group_name FROM user_study_group WHERE study_group_id = id) as study_group_name,            
                        first_name, 
                        last_name, 
                        birthday 
                  FROM user_info 
                  WHERE `user_info`.id = '$id'")->row;
    }

    public function getUserAcademicalGroups($id = null)
    {
        if ($id === null)
            $id = $this->id;
        $agi_rows = $this->db->query("SELECT academical_group_id FROM user_academical_groups WHERE user_id = $id")->rows;
        $agi = array();
        foreach ($agi_rows as $value)
            $agi[] = $value['academical_group_id'];

        return $agi;
    }

    public function reset()
    {
        foreach ($_SESSION as $key => $value)
            unset($_SESSION[$key]);
    }

    public function calculateUserDeviceHash() : string
    {
        return md5('amma-static-salt' . base64_encode($_SERVER['HTTP_USER_AGENT']));
    }

    public function getId() : int
    {
        return $this->id;
    }
}