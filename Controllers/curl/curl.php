<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 24.08.2017
 * Time: 19:06
 */

const BONCH_LOGIN_PAGE = 'app.itut.ru';

class curl extends Base
{
    private $instance;

    protected function init()
    {
        $this->instance = curl_init();
    }

    public function Send($url, array $data, $method = 'GET') : mixed
    {
        if ($method === 'GET')
            foreach ($data as $key => $value)
            {
                $url .= strpos($url, '?') === FALSE ? "?$key=$value" : "&$key=$value";
            }
        else if ($method === 'POST') {
            curl_setopt($this->instance, CURLOPT_POST, true);
            $post_fields = '';
            foreach ($data as $key => $value) {
                $post_fields .= '' === $post_fields ? "$key=$value" : "&$key=$value";
            }
            curl_setopt($this->instance, CURLOPT_POSTFIELDS, $post_fields);
        }

        curl_setopt($this->instance, CURLOPT_URL, $url);
        curl_setopt($this->instance, CURLOPT_RETURNTRANSFER, true);

        return curl_exec($this->instance);
    }
}