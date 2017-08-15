<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 07.08.2017
 * Time: 11:55
 */

//Имеет ли смысл назвать это дело Action?
final class Controller
{
    private $base;
    public function __construct($base)
    {
        $this->base = $base;
    }

    /**
     * @param name - key
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->base->get($name);
    }

    public function __set($name, $value)
    {
        $this->base->set($name, $value);
    }
}