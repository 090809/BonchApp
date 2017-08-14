<?php

const JSON_STRING = 1;
const JSON_PHP_VAR = 2;

const RESPONSE_OK                           = 0x00;
const RESPONSE_BAD_LOGIN                    = 0x01;
const RESPONSE_ERROR_ON_WORK                = 0x02;

final class Response
{
    private $base;
    private $code, $text, $json;
    private $sended = false;

    function __construct($base)
    {
        $this->base = $base;
        $this->code = RESPONSE_OK;
        $this->text = "";
    }

    public function SetCode($code)
    {
        $this->code = $code;
    }

    public function SetText($text)
    {
        $this->text = $text;
    }

    public function SetJson($json, $type = JSON_PHP_VAR)
    {
        if ($type == JSON_PHP_VAR)
            $json = json_encode($json);
        $this->json = $json;
    }

    public function Sended()
    {
        return $this->sended;
    }

    /**
     * @return bool true если Ответ отправляется впервые.
     * Иначе возвращает false
     */
    public function SendResponse()
    {
        $response = array(
            "CODE" => $this->code,
            "TEXT" => $this->text,
            "JSON" => $this->json,
        );
        if (!$this->sended)
            echo json_encode($response);
        else return false;
        $this->sended = true;
        return true;
    }
}