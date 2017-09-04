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
            $this->response->setJson($this->getInfoAboutUser($this->user->get('id')));
        } else {
            $this->response(RESPONSE_USER_NOT_LOGGED_IN);
        }
    }

    public function get()
    {
        global $_BOTH;
        $this->response->setCode(RESPONSE_USER_INFO_GET);
        //$this->user->hasPermission('User/info', 'get')
        if ($this->user->inGroup(USER_GROUP_FULL_ACCESS & !USER_GROUP_LOGGED_IN & !USER_GROUP_STUDENT & !USER_GROUP_ABITURIENT)) {
            $this->response->setJson($this->getInfoAboutUser($_BOTH['id']));
            $this->response->SendResponse();
        } else {
            $this->response(RESPONSE_USER_WRONG_GROUP);
        }
    }

    private function getInfoAboutUser($id) : array
    {
        return $this->db->query("SELECT (SELECT study_group_name FROM user_study_group WHERE study_group_id = id) as study_group_name, first_name, last_name, birthday FROM user_info WHERE id = '$id'")->row;
    }
}