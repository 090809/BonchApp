<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 16.08.2017
 * Time: 13:58
 */

abstract class Base
{
    protected $registry;
    protected function init() {}

    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->init();
    }

    public function __get($name)
    {
        return $this->registry->get($name);
    }

    public function __set($name, $value)
    {
        $this->registry->set($name, $value);
    }
}