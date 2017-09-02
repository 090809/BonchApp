<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 16.08.2017
 * Time: 13:58
 * @property Log|null log
 * @property Response|null response
 * @property DB|null db
 * @property User|null user
 * @property Controller|null controller
 *
 * @method response($ArrayOrCode, $text = '')
 */
abstract class Base
{
    /* @var $registry Registry */
    protected $registry;

    protected function init() {}

    /**
     * Base constructor.
     * @param $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;

        if (null !== $this->log && null !== $this->response) {
            switch (strtolower(get_class($this)))
            {
                case 'action':
                case 'controller':
                case 'parser':
                case 'queue':
                case 'user':
                case 'db':
                    $this->log->logging('BASE [' . get_class($this) . '] LOADED');
                    break;
                default:
                    $this->log->logging('MODULE [' . get_class($this) . '] LOADED');
            }
        }
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

    public function __isset($name)
    {
        return $this->registry->has($name);
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->registry->get($name), '__invoke'))
            ($this->registry->get($name))($arguments);
    }
}