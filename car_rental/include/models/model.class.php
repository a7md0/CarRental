<?php

abstract class Model
{
    protected static $tableName = '';
    protected static $primaryKeys = array();
    protected static $properties = array();

    protected $values = array();

    function __construct()
    {
    }

    /**
     * Undocumented function
     *
     * @param int $value
     * @return self
     */
    function setPrimaryKey($value)
    {
        $this->setValue(static::$primaryKeys[0], $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    function getPrimaryKey()
    {
        return $this->getValue($this->primaryKey);
    }

    /**
     * Undocumented function
     *
     * @param array $keys
     * @return void
     */
    function setPrimaryKeys(...$keys)
    {
        if (count($keys) != count(static::$primaryKeys)) {
            throw new Exception('???');
        }

        for ($i = 0; $i < count($keys); $i++) {
            $this->setValue(static::$primaryKeys[$i], $keys[$i]);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    function getPrimaryKeys()
    {
        return array_map(function ($key) {
            return $this->getValue($key);
        }, static::$primaryKeys);
    }

    /**
     * Set value of given column.
     *
     * @param string $columnName
     * @param mixed $value
     * @return void
     */
    function setValue($columnName, $value)
    {
        if (!array_key_exists($columnName, static::$properties)) {
            throw new Exception(get_class($this) . ' property/parent_object ' . $columnName . ' does not exist');
        }

        $this->values[$columnName] = $value;
    }

    /**
     * Get value of given column.
     *
     * @param string $columnName
     * @return void
     */
    function getValue($columnName)
    {
        return $this->values[$columnName];
    }

    static function findById(...$ids)
    {
        $db = Database::getInstance();
        $tblName = static::$tableName;
        $pkCol = static::$primaryKeys[0];
        $id = $ids[0];

        $result = $db->query("SELECT * FROM `$tblName` WHERE `$pkCol` = $id LIMIT 1;");

        $row = $result->fetch_assoc();
        $model = static::initializeFromData($row);

        $result->free_result();

        return $model;
    }

    /**
     * Save or update the item data in database
     */
    /*function save()
    {
        $class = get_called_class();
        $query =  "REPLACE INTO " . static::$tableName . " (" . implode(",", array_keys($this->columns)) . ") VALUES(";
        $keys = array();
        foreach ($this->columns as $key => $value) {
            $keys[":" . $key] = $value;
        }
        $query .= implode(",", array_keys($keys)) . ")";
        $db = Database::getInstance();
        $s = $db->getPreparedStatment($query);
        $s->execute($keys);
    }*/

    /**
     * Delete this item data from database
     */
    /*function delete()
    {
        $class = get_called_class();
        $query = "DELETE FROM " . static::$tableName . " WHERE " . static::$primaryKey . "=:id LIMIT 1";
        $db = Database::getInstance();
        $s = $db->getPreparedStatment($query);
        $s->execute(array(':id' => $this->columns[static::$primaryKey]));
    }*/

    /**
     * Undocumented function
     *
     * @param array $data
     * @return self
     */
    static function initializeFromData(array $data)
    {
        $modelType = static::class;
        $model = new $modelType;

        foreach ($data as $key => $value) {
            $model->values[$key] = $value;
        }

        return $model;
    }

    /**
     * Get all items
     * Conditions are combined by logical AND
     * @example getAll(array(name=>'Bond',job=>'artist'),'age DESC',0,25) converts to SELECT * FROM TABLE WHERE name='Bond' AND job='artist' ORDER BY age DESC LIMIT 0,25
     */
    /*static function getAll($condition = array(), $order = NULL, $startIndex = NULL, $count = NULL)
    {
        $query = "SELECT * FROM " . static::$tableName;
        if (!empty($condition)) {
            $query .= " WHERE ";
            foreach ($condition as $key => $value) {
                $query .= $key . "=:" . $key . " AND ";
            }
        }
        $query = rtrim($query, ' AND ');
        if ($order) {
            $query .= " ORDER BY " . $order;
        }
        if ($startIndex !== NULL) {
            $query .= " LIMIT " . $startIndex;
            if ($count) {
                $query .= "," . $count;
            }
        }
        return self::get($query, $condition);
    }*/

    /**
     * Pass a custom query and condition
     * @example get('SELECT * FROM TABLE WHERE name=:user OR age<:age',array(name=>'Bond',age=>25))
     */
    /*static function get($query, $condition = array())
    {
        $db = Database::getInstance();
        $s = $db->getPreparedStatment($query);
        foreach ($condition as $key => $value) {
            $condition[':' . $key] = $value;
            unset($condition[$key]);
        }
        $s->execute($condition);
        $result = $s->fetchAll(PDO::FETCH_ASSOC);
        $collection = array();
        $className = get_called_class();
        foreach ($result as $row) {
            $item = new $className();
            $item->createFromData($row);
            array_push($collection, $item);
        }
        return $collection;
    }*/

    /**
     * Get a single item
     */
    /*static function getOne($condition = array(), $order = NULL, $startIndex = NULL)
    {
        $query = "SELECT * FROM " . static::$tableName;
        if (!empty($condition)) {
            $query .= " WHERE ";
            foreach ($condition as $key => $value) {
                $query .= $key . "=:" . $key . " AND ";
            }
        }
        $query = rtrim($query, ' AND ');
        if ($order) {
            $query .= " ORDER BY " . $order;
        }
        if ($startIndex !== NULL) {
            $query .= " LIMIT " . $startIndex . ",1";
        }
        $db = Database::getInstance();
        $s = $db->getPreparedStatment($query);
        foreach ($condition as $key => $value) {
            $condition[':' . $key] = $value;
            unset($condition[$key]);
        }
        $s->execute($condition);
        $row = $s->fetch(PDO::FETCH_ASSOC);
        $className = get_called_class();
        $item = new $className();
        $item->createFromData($row);
        return $item;
    }*/

    /**
     * Get an item by the primarykey
     */
    /*static function getByPrimaryKey($value)
    {
        $condition = array();
        $condition[static::$primaryKeys[0]] = $value;
        return self::getOne($condition);
    }*/

    /**
     * Get the number of items
     */
    /*static function getCount($condition = array())
    {
        $query = "SELECT COUNT(*) FROM " . static::$tableName;
        if (!empty($condition)) {
            $query .= " WHERE ";
            foreach ($condition as $key => $value) {
                $query .= $key . "=:" . $key . " AND ";
            }
        }
        $query = rtrim($query, ' AND ');
        $db = Database::getInstance();
        $s = $db->getPreparedStatment($query);
        foreach ($condition as $key => $value) {
            $condition[':' . $key] = $value;
            unset($condition[$key]);
        }
        $s->execute($condition);
        $countArr = $s->fetch();
        return $countArr[0];
    }*/
}
