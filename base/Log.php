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
    private $file;

    protected function init()
    {
        $time = $this->getTime();
        $this->file = __DIR_INDEX__ . '/Logs/' . session_id() . '.log';

        if (isset($_SESSION['id']))
        {
            $file_name_new = __DIR_INDEX__ . '/Logs/' . $_SESSION['id'] . '.log';
            if (file_exists($this->file) && !file_exists($file_name_new))
                rename($this->file, $file_name_new);
            else if (file_exists($file_name_new)) {
                file_put_contents($file_name_new, file_get_contents($this->file), FILE_APPEND | LOCK_EX);
                unlink($this->file);
            }

            $this->file = $file_name_new;
        }

        if (file_exists($this->file))
            file_put_contents($this->file, "\n\n NEW CONNECTION IN $time \n", FILE_APPEND | LOCK_EX);
    }

    public function logging($_text, $type = LOG_MESSAGE)
    {
        $time = $this->getTime();
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
                $text .= 'MESSAGE: ' . $_text;
                break;
        }
        $this->writeToFile($text);

        if (DEBUG || LOG_ERROR === $type)
            $this->writeToJSON($text);
    }

    public function __invoke($_text, $type = LOG_MESSAGE)
    {
        $this->logging($_text, $type);
    }

    private function writeToFile($text)
    {
        file_put_contents($this->file, $text . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    private function writeToJSON($text)
    {
        $this->response->setJson($text);
    }

    private function getTime() : string
    {
        list($usec, $sec) = explode(' ', microtime());
        $time = date('Y-m-d H:i:s', $sec);
        $usec = (int)explode('.', $usec)[1];
        $time .= "::$usec";
        return $time;
    }
}