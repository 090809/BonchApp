<?php

/** @noinspection AutoloadingIssuesInspection */
final class DB {
    private $driver, $registry;

    public function __construct($driver, $hostname, $username, $password, $database, $registry) {
        if (file_exists( __DIR__ . '/database/' . $driver . '.php')) {
            /** @noinspection PhpIncludeInspection */
            require_once __DIR__ . '/database/' . $driver . '.php';
        } else {
            exit('Error: Could not load database file ' . $driver . '!');
        }
        $this->driver = new $driver($hostname, $username, $password, $database);
        $this->registry = $registry;
    }

    public function query($sql) {
        //if (DEBUG && $this->registry->get('log'))
        //    $this->registry->get('log')->logging($sql);

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