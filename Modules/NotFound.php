<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 16.08.2017
 * Time: 23:09
 */

class NotFound extends Base
{
    public function index()
    {
        $this->response->setCode(RESPONSE_NOT_FOUND);
        $this->response->setText('Файл или класс не найден');
        $this->response->SendResponse();
    }
}