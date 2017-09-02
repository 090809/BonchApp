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
            $this->Response(RESPONSE_ALREADY_LOGGED_IN);
        } else {
            $this->Response(RESPONSE_NOT_LOGGED_IN);
        }
    }

    /**
     * @return bool
     */
    public function byHash(): bool
    {
        global $_BOTH;
        if ($this->user->isLoggedIn())
        {
            $this->Response(RESPONSE_ALREADY_LOGGED_IN);
            return true;
        }

        if (isset($_BOTH['u_hash'], $_BOTH['ud_hash'])) {
            $query = $this->db->query("SELECT * FROM `user` WHERE `hash` = '$_BOTH[u_hash]' AND `userdevice_hash` = '$_BOTH[ud_hash]'");
            if ($query->num_rows)
            {
                $this->user->setGroup($query->row['group']);
                $this->user->setHash($query->row['hash']);
                $this->Response(RESPONSE_LOGGED_IN);
                return true;
            }
        }
        $this->Response(RESPONSE_LOGIN_FAILED, 'Combination of [u_hash] and [ud_hash] not found, try login by UserName');
        return false;
    }

    public function byPass()
    {
        global $_BOTH;
        if ($this->user->isLoggedIn())
        {
            $this->Response(RESPONSE_ALREADY_LOGGED_IN);
            return true;
        }

        if (isset($_BOTH['username'], $_BOTH['password'], $_BOTH['ud_hash']))
        {
            $this->controller->load('curl/curl');
            $response = json_decode($this->controller_curl->send(BONCH_LOGIN_PAGE, array($_BOTH['username'], $_BOTH['password'])));
            if ($response->hash)
            {
                $this->db->query("INSERT INTO `user` (`hash`, `ud_hash`, `group`) VALUES ('$response->hash', '$_BOTH[ud_hash]', '$response->group')");
                $this->user->setGroup($response->group);
                $this->user->setHash($response->hash);

                $json = new stdClass();
                $json->hash = $response->hash;
                $this->response->setJson($json);
                $this->Response(RESPONSE_LOGGED_IN);
                return true;
            }
        }
        $this->Response(RESPONSE_LOGIN_FAILED, 'Combination of login-password is wrong, or main Bonch Server Service is down');
        return false;
    }
}