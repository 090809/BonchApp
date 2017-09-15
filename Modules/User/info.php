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
        if ($this->user->hasPermission('User/info', 'get')) {
        //if ($this->user->inGroup(USER_GROUP_FULL_ACCESS & !USER_GROUP_LOGGED_IN & !USER_GROUP_STUDENT & !USER_GROUP_ABITURIENT)) {
            if ($this->user->inPermGroup(USER_GROUP_HEAD_STUDENT) && !$this->user->get('study_group_id') === $_BOTH['id'])
                $this->response(RESPONSE_USER_ACCESS_DENIED);

            $this->response->setJson($this->getInfoAboutUser($_BOTH['id']));
            $this->response->SendResponse();
        } else if ($this->user->get('id') === (int)$_BOTH['id']) {
            $this->response->setJson($this->getInfoAboutUser($_BOTH['id']));
            $this->response->SendResponse();
        } else {
            $this->response(RESPONSE_USER_ACCESS_DENIED);
        }
    }

    public function update()
    {
        $this->user->getInfoAboutUser(true);
    }

    private function getInfoAboutUser($id) : array
    {
        return $this->db->query("SELECT (SELECT study_group_name FROM user_study_group WHERE study_group_id = id) as study_group_name, first_name, last_name, birthday FROM user_info WHERE id = '$id'")->row;
    }

    public function getUDHash()
    {
        var_dump($_SERVER['HTTP_USER_AGENT']);
        echo '<br>';
        $this->response->setJson($this->user->calculateUserDeviceHash());
        $this->response(RESPONSE_USER_INFO_GET);
    }
}