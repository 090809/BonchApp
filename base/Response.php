<?php

const JSON_STRING = 1;
const JSON_PHP_VAR = 2;

//$__PARSER->ParseAndExecFile(__DIR_INDEX__ . '/help/codes', array('enum', '//', '{', '}'), array( 'RESPONSE' => 'const RESPONSE', ',' => ';') );

require_once __DIR_INDEX__ . '/System/ResponseCodes.php';

final class Response extends Base
{
    private $code, $text, $sended = false,
            $json = array(),
            $json_debug = array();

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

    public function setJson($json, $debug = false)
    {
        if ($debug)
            $this->json_debug[] = $json;
        else
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
        $response = array();

        $response['CODE'] = $this->code;
        $response['JSON'] = $this->json;
        if (DEBUG || $this->user->get('allow_debug')) {
            $response['TEXT'] = $this->text;
            $response['JSON_DEBUG'] = $this->json_debug;
        }
        if ($this->sended) {
            return false;
        }

        echo json_encode($response);
        $this->sended = true;
        return true;
    }
}