<?php
$__PARSER = new Parser($base);

$P = &$__PARSER;

$_GET = &$__PARSER->GET;
$_POST = &$__PARSER->POST;
$_BOTH = &$__PARSER->BOTH;

const MAX_VALUES_PER_ARRAY = 30;

final class Parser {

    public $GET        = array();
    public $POST       = array();
    public $BOTH       = array();
    private $base, $log;

    function __construct($base)
    {
        $this->base = $base;
        $this->log  = $base->log;
        $this->loadValues($_GET);
        $this->loadValues($_POST, "POST");
    }

    public function __get($name)
    {
        return $this->base->get($name);
    }

    public function __set($name, $value)
    {
        $this->base->set($name, $value);
    }

    public function __invoke($key)
    {
        return $this->BOTH[$key];
    }

    private function loadValues($array, $type = "GET")
    {
        $counter = 0;
        foreach ($array as $key => $value)
        {
            if (++$counter < MAX_VALUES_PER_ARRAY)
            {
                /** На самом деле - есть ли ВООБЩЕ смысл трогать post и get запросы? */
                //На этом моменте уже существует прямой коннект к бд.
                //Имеет ли смысл пытаться здесь сразу проверить и на экранизацию к бд?
                if ($type == "GET") {
                    $this->GET[$key] = $this->db->escape($value);
                    $this->BOTH[$key] = $this->GET[$key];
                }
                else {
                    $this->POST[$key] = $this->db->escape($value);
                    if (isset($this->BOTH[$key]))
                    {
                        ($this->log)("{PARSER:POST}: ключ '$key' уже был установлен. Перезапись.", LOG_WARNING);
                    }
                    $this->BOTH[$key] = $this->db->escape($value);
                }
            }
        }
    }

    /**
     * @param $type                 // GET or POST
     * @param $key                  // Key of array
     * @return mixed|string|""      // Object if can, else return string (or empty string)
     */
    public function GetFromJSON($type, $key)
    {
        switch ($type)
        {
            case "GET":
                $var = json_decode($this->GET[$key]);
                if (is_null($var))
                    return $this->GET[$key];
                else return $var;
            case "POST":
                $var = json_decode($this->POST[$key]);
                if (is_null($var))
                    return $this->POST[$key];
                else return $var;
            default:
                return "";
        }
    }
}