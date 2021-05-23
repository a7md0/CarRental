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


    /**
     * Prepare and execute statements and bind passed types and values.
     *
     * @param string $query
     * @param string $types
     * @param array $values
     * @return mysqli_stmt|false
     */
    public static function executeStatement($query, $types = '', array $values = [])
    {
        $db = static::getInstance();
        $stmt = $db->prepare($query);

        if (strlen($types) > 0 && count($values) > 0) {
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();

        return $stmt;
    }

    public function closeConnection()
    {
        if (self::$instance != null) {
            parent::close();
            self::$instance = null;
        }
    }
}
