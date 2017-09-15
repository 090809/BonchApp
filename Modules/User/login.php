<?php

/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 17.08.2017
 * Time: 0:05
 * @property curl|null controller_curl
 */
class login extends Base
{
    public function index()
    {
        if ($this->user->isLoggedIn())
        {
            $this->response(RESPONSE_USER_ALREADY_LOGGED_IN);
        } else {
            $this->response(RESPONSE_USER_NOT_LOGGED_IN);
        }
    }

    /**
     * @return bool
     */
    public function byHash(): bool
    {
        global $_BOTH;
        if (!isset($_BOTH['force_login']) && $this->user->isLoggedIn())
        {
            $this->response(RESPONSE_USER_ALREADY_LOGGED_IN);
            return true;
        }

        if (isset($_BOTH['force_login']))
            $this->user->reset();

        if (isset($_BOTH['u_hash'])) {
            $ud_hash = md5('amma-static-salt' . base64_encode($_SERVER['HTTP_USER_AGENT']));

            $query = $this->db->query("SELECT * FROM `user` WHERE `hash` = '$_BOTH[u_hash]' AND `userdevice_hash` = '$ud_hash'");
            if ($query->num_rows)
            {
                $this->user->set('id', $query->row['id']);
                $this->user->setPermGroup($query->row['group']);
                $this->user->setHash($query->row['hash']);
                $this->response(RESPONSE_USER_LOGGED_IN);
                return true;
            }
        }
        $this->response(RESPONSE_USER_LOGIN_FAILED, 'Combination of [u_hash] and [ud_hash] not found, try login by UserName');
        return false;
    }

    public function byPass()
    {
        global $_BOTH;
        if (!isset($_BOTH['force_login']) && $this->user->isLoggedIn())
        {
            $this->response(RESPONSE_USER_ALREADY_LOGGED_IN);
            return true;
        }

        if (isset($_BOTH['force_login']))
            $this->user->reset();

        if (isset($_BOTH['username'], $_BOTH['password']))
        {
            $ud_hash = md5('amma-static-salt' . base64_encode($_SERVER['HTTP_USER_AGENT']));

            //@TODO: Сходить в АСУ и выбить из них коннект для базы.
            //$this->controller->load('curl/curl');
            //$response = json_decode($this->controller_curl->Send(BONCH_LOGIN_PAGE, array($_BOTH['username'], $_BOTH['password'])));

            {
                $response = new stdClass();
                $response->hash = md5('..' . md5("$_BOTH[username] .. $_BOTH[password]"));
                $response->group = USER_GROUP_FULL_ACCESS;
            }

            if ($response->hash)
            {
                //@TODO: Необходимо сопоставлять ЧЕЛОВЕКА и ДАННЫЕ ВХОДА!
                //$user_info_id = $response->id;
                $this->db->query("INSERT INTO `user` (`hash`, `userdevice_hash`, `group`) VALUES ('$response->hash', '$ud_hash', '$response->group')");

                $this->user->set('id', $this->db->getLastId());
                $this->user->setPermGroup($response->group);
                $this->user->setHash($response->hash);


                //Получение основной информации о пользователе.
                $this->user->getInfoAboutUser(true);

                $json = new stdClass();
                $json->hash = $response->hash;
                $this->response->setJson($json);
                $this->response(RESPONSE_USER_LOGGED_IN);
                return true;
            }
        }
        $this->response(RESPONSE_USER_LOGIN_FAILED, 'Combination of login-password is wrong, or main Bonch Server Service is down');
        return false;
    }
}