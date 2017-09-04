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


    /**
     * @param $path
     * @param $escape string|array
     * @param $replacement string|array
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function ParseAndExecFile($path, $escape, $replacement) : bool
    {
        $eval = '';
        if (file_exists($path))
        {
            $handle = fopen($path, 'rb');
            while (($buffer = fgets($handle)) !== FALSE)
            {
                if ($this->findEscape($buffer, $escape)) continue;
                $eval .= $this->useReplacement($buffer, $replacement) . "\n";
            }
            fclose($handle);

            try {
                eval($eval);
            } catch (Exception $e) {
                if (DEBUG)
                    var_dump($e);
            }
            return true;
        }
        return false;
    }

    /**
     * @param string $string
     * @param string|array $escape
     * @return bool
     */
    private function findEscape($string, $escape) : bool
    {
        if (is_array($escape))
        {
            /** @noinspection ForeachSourceInspection */
            //Убрано из-за проверки на array выше.
            foreach ($escape as $value)
                if (strpos($string, $value) !== false)
                    return true;

        } else if (strpos($string, $escape) !== false)
            return true;

        return false;
    }

    /**
     * @param $string
     * @param array $replacement
     * @return string
     * @throws InvalidArgumentException if $replacement is not array
     */
    private function useReplacement($string, $replacement) : string
    {
        if (is_array($replacement)) {
            foreach ($replacement as $name => $to)
                $string = str_replace($name, $to, $string);

            return $string;
        }
        throw new InvalidArgumentException('$replacement is not array');
    }
}