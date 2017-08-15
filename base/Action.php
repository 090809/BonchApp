<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 09.08.2017
 * Time: 16:34
 */

final class Action
{
    private $base;
    private $file, $class, $func;

    public function __construct($base, $file = null, $class = null, $func = null)
    {
        global $_BOTH;
        $this->base = $base;
        if ($file != null)
        {
            if (isset($_BOTH['file']) && isset($_BOTH['class']))
            {
                $this->file     = str_replace(array("../", "..", "//"), "", $_BOTH['file']);
                $this->class    = $_BOTH['class'];
                if (isset($_BOTH['func']) && $_BOTH['func'] != "")
                    $this->func = $_BOTH['func'];
                else
                    $this->func = "index";
            }
            else
            {
                $this->file = "error";
                $this->class = "error";
                $this->func = "index";
            }
        }
        else
        {
            $this->file     = str_replace(array("../", "..", "//"), "", $file);
            $this->class    = $class;
            if (isset($func) && $func != "")
                $this->func = $func;
            else
                $this->func = "index";
        }

        $this->file = __DIR_MODULES__ . $this->file . ".php";
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