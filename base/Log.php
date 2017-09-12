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

    public function logging($text, $type = LOG_MESSAGE)
    {
        $time = $this->getTime();
        $_text = "[$time] ";
        switch ($type)
        {
            case LOG_ERROR:
                $_text .= 'ERROR: ' . $text;
                break;
            case LOG_WARNING:
                $_text .= 'WARNING: ' . $text;
                break;
            case LOG_MESSAGE:
                $_text .= 'MESSAGE: ' . $text;
                break;
        }
        $this->writeToFile($_text);
        $this->writeToJSON($_text);
    }

    public function __invoke($_text, $type = LOG_MESSAGE)
    {
        $this->logging($_text, $type);
    }

    /**
     * @param $text
     */
    private function writeToFile($text)
    {
        file_put_contents($this->file, $text . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    /**
     * @param $text
     */
    private function writeToJSON($text)
    {
        if ($this->response !== null)
            $this->response->setJson($text, true);
    }

    private function getTime() : string
    {
        list($millis, $sec) = explode(' ', microtime());
        $time = date('Y-m-d H:i:s', $sec);
        $millis = (int)explode('.', $millis)[1];
        $time .= "::$millis";
        return $time;
    }
}