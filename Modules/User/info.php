<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 04.09.2017
 * Time: 14:30
 */

class info extends Base
{
    public function index()
    {
        if ($this->user->isLoggedIn())
        {
            $this->response->setCode(RESPONSE_USER_INFO);
            $this->response->setJson($this->user->getInfoAboutUser());
        } else {
            $this->response(RESPONSE_USER_NOT_LOGGED_IN);
        }
    }

    public function get()
    {
        global $_BOTH;
        $this->response->setCode(RESPONSE_USER_INFO_GET);
        if ($this->user->get('id') === $_BOTH['id']) {
            $this->response->setJson($this->user->getOrUpdateInfoAboutUser());
            $this->response->SendResponse();
        } else if ($this->user->hasPermission('User/info', 'get')) {
            if ($this->user->inPermGroup(USER_GROUP_HEAD_STUDENT) && !$this->user->get('study_group_primary') === $_BOTH['id'])
                $this->response(RESPONSE_USER_ACCESS_DENIED);

            $this->response->setJson($this->user->getInfoAboutUser($_BOTH['id']));
            $this->response->SendResponse();
        } else {
            $this->response(RESPONSE_USER_ACCESS_DENIED);
        }
    }

    public function update()
    {
        $this->user->getOrUpdateInfoAboutUser(true);
    }

    public function getUDHash()
    {
        var_dump($_SERVER['HTTP_USER_AGENT']);
        echo '<br>';
        $this->response->setJson($this->user->calculateUserDeviceHash());
        $this->response(RESPONSE_USER_INFO_GET);
    }
}