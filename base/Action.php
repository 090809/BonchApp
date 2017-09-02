<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 09.08.2017
 * Time: 16:34
 */

final class Action extends Base
{
    private $file, $class, $func;
    private $permission;

    public function __construct($registry, $file = null, $class = null, $func = null)
    {
        parent::__construct($registry);

        global $_BOTH;
        //Внешняя (web) или внутренняя (вызов) передача в __конструктор
        if (null === $file)
        {

            if (isset($_BOTH['file'], $_BOTH['class']))
            {
                $this->file     = str_replace(array('../', '..', '//', '\\'), '', $_BOTH['file']);
                $this->class    = $_BOTH['class'];
                $this->func = isset($_BOTH['func']) && $_BOTH['func'] !== '' ? $_BOTH['func'] : 'index';

                if (!file_exists(__DIR_MODULES__ . $this->file . '.php'))
                {
                    $this->class    = 'NotFound';
                    $this->file     = 'NotFound';
                    $this->func     = 'index';
                }
            }
            else
            {
                $this->file     = 'NotFound';
                $this->class    = 'NotFound';
                $this->func     = 'index';
            }
        }
        else
        {
            $this->file     = str_replace(array('../', '..', '//', "\\"), '', $file);
            $this->class    = $class;
            $this->func = isset($func) && $func !== '' ? $func : 'index';
        }

        $this->permission = $this->db->query("  SELECT permission
                                                FROM user_group_permission 
                                                WHERE file = '$this->file' 
                                                AND class = '$this->class' 
                                                AND func = '$this->func'
                                                LIMIT 0, 1");

        if ($this->permission->num_rows === 0) {
            $this->permission = USER_GROUP_NONE;
        } else {
            $this->permission = $this->permission->row('permission');
        }

        $this->file = __DIR_MODULES__ . $this->file . '.php';
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getFunc()
    {
        return $this->func;
    }
}