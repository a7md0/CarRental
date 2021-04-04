<?php
class Database extends MySQLi
{
    /**
     * Singleton instance of MySQLi
     *
     * @var self
     */
    private static $instance = null;

    private function __construct($host, $user, $password, $database)
    {
        parent::__construct($host, $user, $password, $database);
    }

    /**
     * Instance of MySQLi
     *
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        }

        return self::$instance;
    }
}
