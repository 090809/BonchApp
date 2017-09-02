<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 07.08.2017
 * Time: 11:55
 */
const __DIR_CONTROLLERS__ = __DIR_INDEX__ . 'Controllers/';
//Имеет ли смысл назвать это дело Action?
final class Controller extends Base
{
    /**
     * @param $path
     */
    public function load($path)
    {
        $parts = explode ('/', $path);
        $file = 'index';
        $class = 'index';
        $func = 'index';
        switch (count($parts))
        {
            //only filename
            case 1:
                $file = $parts[0];
                /** @noinspection MultiAssignmentUsageInspection */
                $class = $parts[0];
                break;
            case 2:
                $file = "$parts[0]/$parts[1]";
                $class = $parts[1];
                break;
            case 3:
                $file = "$parts[0]/$parts[1]";
                $class = $parts[1];
                /** @noinspection MultiAssignmentUsageInspection */
                $func = $parts[2];
                break;
        }
        if (file_exists(__DIR_CONTROLLERS__ . $file . '.php')) {
            /** @noinspection PhpIncludeInspection */
            require_once __DIR_CONTROLLERS__ . $file . '.php';
            $o_class = new $class();
            $this->registry->set("controller_$class", $o_class);
            if (is_callable(array($o_class, $func))) {
                $o_class->$func();
            }
        }
    }
}