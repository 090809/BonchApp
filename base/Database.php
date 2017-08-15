<?php

final class DB {
    private $driver;

    public function __construct($driver, $hostname, $username, $password, $database) {
        if (file_exists( __DIR__ . "/database/" . $driver . '.php')) {
            require_once(__DIR__ . "/database/" . $driver . '.php');
        } else {
            exit('Error: Could not load database file ' . $driver . '!');
        }
        $this->driver = new $driver($hostname, $username, $password, $database);
    }

    public function query($sql) {
        return $this->driver->query($sql);
    }

    public function escape($value) {
        return $this->driver->escape($value);
    }

    public function countAffected() {
        return $this->driver->countAffected();
    }

    public function getLastId() {
        return $this->driver->getLastId();
    }
}