<?php
/**
 * Created by PhpStorm.
 * User: darkm
 * Date: 09.08.2017
 * Time: 20:57
 */

class DBMySQLi
{
    private $link;

    public function __construct($hostname, $username, $password, $database) {
        $this->link = new mysqli($hostname, $username, $password, $database);

        if (mysqli_connect_error()) {
            throw new ErrorException('Error: Could not make a database link (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        }

        $this->link->set_charset('utf8');
        $this->link->query("SET SQL_MODE = ''");
    }

    /**
     * @param $sql
     * @return bool|stdClass
     * @throws ErrorException
     */
    public function query($sql) {
        $query = $this->link->query($sql);

        if (!$this->link->errno){
            if (isset($query->num_rows)) {
                $data = array();

                while ($row = $query->fetch_assoc()) {
                    $data[] = $row;
                }

                $result = new stdClass();
                $result->num_rows = $query->num_rows;
                $result->row = $data[0] ?? array();
                $result->rows = $data;

                unset($data);

                $query->close();

                return $result;
            }

            return true;
        }

        throw new ErrorException('Error: ' . $this->link->error . '<br />Error No: ' . $this->link->errno . '<br />' . $sql);
        //exit();
    }

    public function escape($value): string
    {
        return $this->link->real_escape_string($value);
    }

    public function countAffected(): int
    {
        return $this->link->affected_rows;
    }

    public function getLastId() : int
    {
        return $this->link->insert_id;
    }

    public function __destruct() {
        $this->link->close();
    }
}