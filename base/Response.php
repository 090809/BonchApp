<?php

const JSON_STRING = 1;
const JSON_PHP_VAR = 2;

//Statement of work
const RESPONSE_OK                           = 0x00;
const RESPONSE_ERROR_ON_WORK                = 0x01;
const RESPONSE_NOT_FOUND                    = 0x02;

//Statement of User
const RESPONSE_BAD_LOGIN                    = 0x03;
const RESPONSE_ALREADY_LOGGED_IN            = 0x04;
const RESPONSE_NOT_LOGGED_IN                = 0x05;
const RESPONSE_LOGGED_IN                    = 0x06;
const RESPONSE_LOGIN_FAILED                 = 0x07;

final class Response extends Base
{
    private $code, $text;
    private $json = array();
    private $sended = false;

    public function __invoke($ArrayOrCode, $text = '')
    {
        if (is_array($ArrayOrCode))
        {
            $this->setCode($ArrayOrCode[0]);
            if (isset($ArrayOrCode[1]))
                $this->setText($ArrayOrCode[1]);
        } else {
            $this->setCode($ArrayOrCode);
            $this->setText($text);
        }
        $this->SendResponse();
    }

    protected function init()
    {
        $this->code = RESPONSE_OK;
        $this->text = '';
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function setJson($json)
    {
        $this->json[] = $json;
    }

    public function sended()
    {
        return $this->sended;
    }

    /**
     * @return bool true если Ответ отправляется впервые.
     * Иначе возвращает false
     */
    public function SendResponse(): bool
    {
        $response = array(
            'CODE' => $this->code,
            'TEXT' => $this->text,
            'JSON' => $this->json,
        );
        if ($this->sended) {
            return false;
        }

        echo json_encode($response);
        $this->sended = true;
        return true;
    }
}