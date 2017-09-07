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

    public function __construct($registry, $file = null, $class = null, $func = null)
    {
        parent::__construct($registry);

        global $_BOTH;
        //Внешняя (web) или внутренняя (вызов) передача в __конструктор
        if (null === $file)
        {

            if (isset($_BOTH['file']))
            {
                $this->file     = str_replace(array('../', '..', '//', '\\'), '', $_BOTH['file']);

                if (isset($_BOTH['class']))
                    $this->class = $_BOTH['class'];
                else {
                    $temp = explode('/', $this->file);
                    $this->class = $temp[count($temp) - 1];
                }

                $this->func = isset($_BOTH['func']) && $_BOTH['func'] !== '' ? $_BOTH['func'] : 'index';

                if (!file_exists(__DIR_MODULES__ . $this->file . '.php'))
                {
                    $this->file     = 'NotFound';
                    $this->class    = 'NotFound';
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
            $this->func     = ($func !== null && $func !== '') ? $func : 'index';
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