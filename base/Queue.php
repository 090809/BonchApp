<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 16.08.2017
 * Time: 22:53
 */

class Queue extends Base
{
    private $Actions = array();

    protected function init()
    {
        $this->Actions[] = new Action($this->registry);
        while ($Action = array_shift($this->Actions))
        {
            if (file_exists($Action->getFile()))
            {
                /** @noinspection PhpIncludeInspection */
                require_once $Action->getFile();

                $class = $Action->getClass();
                $o_class = new $class($this->registry);

                $this->registry->set('module_' . $Action->getClass(), $o_class);

                if ($this->user->hasPermission($Action->getModuleFile(), $Action->getFunc())) {
                    if (is_callable(array($o_class, $Action->getFunc()))) {
                        $o_class->{$Action->getFunc()}();
                    } else {
                        $this->response->setCode(RESPONSE_ERROR_ON_WORK);
                        $this->response->setText('Вызываемая функция не найдена');
                        $this->response->SendResponse();
                        break;
                    }
                } else {
                    $this->response(RESPONSE_USER_ACCESS_DENIED);
                }
            }
        }
    }
}