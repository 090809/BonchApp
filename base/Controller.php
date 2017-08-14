<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 07.08.2017
 * Time: 11:55
 */

//Имеет ли смысл назвать это дело Action?
class Controller
{
    protected $system;
    private $path, $module, $function;

    public function __construct()
    {
        $this->path = $this->parser->GET['path'];
        $local_path = explode('/', $this->path);
        if ($local_path[0] != "")
            $this->module = $local_path[0];
        if ($local_path[1] != "")
            $this->function = $local_path[1];
    }

    public function __get($name)
    {
        return $this->system->get($name);
    }

    public function __set($name, $value)
    {
        $this->system->set($name, $value);
    }
}