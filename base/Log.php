<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 09.08.2017
 * Time: 16:34
 */

const LOG_ERROR         = 0;
const LOG_WARNING       = 1;
const LOG_MESSAGE       = 2;

final class Log extends Base
{
    public function logging($_text, $type = LOG_MESSAGE)
    {
        $time = date('Y-m-d H:i:s');
        $text = "[$time] ";
        switch ($type)
        {
            case LOG_ERROR:
                $text .= 'ERROR: ' . $_text;
                break;
            case LOG_WARNING:
                $text .= 'WARNING: ' . $_text;
                break;
            case LOG_MESSAGE:
                $text .= 'LOG: ' . $_text;
                break;
        }
        $this->writeToFile($text);
        $this->writeToJSON($text, $type);
    }

    public function __invoke($_text, $type = LOG_MESSAGE)
    {
        $this->logging($_text, $type);
    }

    private function writeToFile($text)
    {

    }

    private function writeToJSON($text, $type)
    {
        if (DEBUG || LOG_ERROR === $type)
            $this->response->setJson($text);
    }
}