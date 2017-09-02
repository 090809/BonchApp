<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 09.08.2017
 * Time: 16:33
 */

//Этот класс необходим для работы всех модулей.
//Он загружается в остальные, для упрощения доступа к другим модулям, которые обязаны быть доступными извне
final class Registry {
    private $data = array();

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
    
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        return ($this->data[$key] ?? NULL);
    }

    /**
     * @param $key
     * @param $value
     * @return mixed $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $value;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key): bool
    {
        return isset($this->data[$key]);
    }
}