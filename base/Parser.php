<?php

const MAX_VALUES_PER_ARRAY = 30;

$__PARSER = new Parser($registry);

$P = &$__PARSER;

$_GET = &$__PARSER->GET;
$_POST = &$__PARSER->POST;
$_BOTH = &$__PARSER->BOTH;

/**
 * @property DB|null db
 */
final class Parser extends Base {

    public $GET        = array();
    public $POST       = array();
    public $BOTH       = array();

    protected function init() {
        $this->loadValues($_GET, 'GET');
        $this->loadValues($_POST, 'POST');
    }

    public function __invoke($key)
    {
        return $this->BOTH[$key];
    }

    private function loadValues($array, $type)
    {
        $counter = 0;

        /** @noinspection ForeachSourceInspection */
        foreach ($array as $key => $value)
        {
            if (++$counter < MAX_VALUES_PER_ARRAY)
                if ($type === 'GET') {
                    $this->GET[$key] = $this->db->escape($value);
                    $this->BOTH[$key] = $this->GET[$key];
                }
                else {
                    $this->POST[$key] = $this->db->escape($value);
                    if (isset($this->BOTH[$key]))
                    {
                        $this->log->logging("{PARSER:POST}: ключ '$key' уже был установлен. Перезапись.", LOG_WARNING);
                    }
                    $this->BOTH[$key] = $this->db->escape($value);
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
            case 'GET': {
                $var = json_decode($this->GET[$key]);
                if (null !== $var) {
                    return $var;
                }
                return $this->GET[$key];
            } break;
            case 'POST': {
                $var = json_decode($this->POST[$key]);
                if (null !== $var) {
                    return $var;
                }
                return $this->POST[$key];
            } break;
            default:
                return '';
        }
    }
}