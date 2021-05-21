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
     * Initialize new object from the provided data.
     *
     * @param array $data
     * @return self
     */
    static function initializeFromData(array $data)
    {
        $modelType = static::class;
        $model = new $modelType;

        foreach ($data as $key => $value) {
            if (in_array($key, static::$properties)) {
                $model->values[$key] = $value;
            }
        }

        return $model;
    }

    /**
     * Find one matching record with the provided identifer(s).
     *
     * @param WhereClause $where
     * @return self|null
     */
    static function findById(...$ids)
    {
        $whereClause = new WhereClause();
        $primaryKeys = static::$primaryKeys;

        for ($i = 0; $i < count($primaryKeys); $i++) {
            $whereClause->where($primaryKeys[$i], $ids[$i]);
        }

        $model = static::findOne($whereClause);

        if ($model == null) {
            throw new Exception("No record is matching the provided identifiers", 404);
        }

        return $model;
    }

    /**
     * Find any matching records with the provided condition(s).
     *
     * @param WhereClause $where
     * @return self|null
     */
    static function find(WhereClause $where = null)
    {
        $tblName = static::$tableName;

        $whereClause = '';
        $whereTypes = '';
        $whereValues = [];

        if (isset($where) && $where->hasAny()) {
            $whereClause = ' ' . $where->getSQL();
            $whereTypes = $where->getTypes();
            $whereValues = $where->getValues();
        }

        $query = "SELECT * FROM `$tblName`$whereClause;";

        $stmt = static::executeStatement($query, $whereTypes, $whereValues);
        $models = [];

        if ($result = $stmt->get_result()) {
            while ($row = $result->fetch_assoc()) {
                $models[] = static::initializeFromData($row);
            }
        }

        $stmt->free_result();
        $stmt->close();

        return $models;
    }

    /**
     * Find one matching record with the provided condition(s).
     *
     * @param WhereClause $where
     * @return self|null
     */
    static function findOne(WhereClause $where = null)
    {
        $tblName = static::$tableName;

        $whereClause = '';
        $whereTypes = '';
        $whereValues = [];

        if (isset($where) && $where->hasAny()) {
            $whereClause = ' ' . $where->getSQL();
            $whereTypes = $where->getTypes();
            $whereValues = $where->getValues();
        }

        $query = "SELECT * FROM `$tblName`$whereClause LIMIT 1;";

        $stmt = static::executeStatement($query, $whereTypes, $whereValues);
        $model = null;

        if ($result = $stmt->get_result()) {
            $row = $result->fetch_assoc();

            if ($row != null) {
                $model = static::initializeFromData($row);
            }
        }

        $stmt->free_result();
        $stmt->close();

        return $model;
    }

    /**
     * Count matching rows and return count.
     *
     * @param WhereClause $where
     * @return int
     */
    static function count(WhereClause $where = null)
    {
        $tblName = static::$tableName;

        $whereClause = '';
        $whereTypes = '';
        $whereValues = [];

        if (isset($where) && $where->hasAny()) {
            $whereClause = ' ' . $where->getSQL();
            $whereTypes = $where->getTypes();
            $whereValues = $where->getValues();
        }

        $query = "SELECT COUNT(*) FROM `$tblName`$whereClause;";

        $stmt = static::executeStatement($query, $whereTypes, $whereValues);
        $count = 0;

        if ($result = $stmt->get_result()) {
            $row = $result->fetch_row();

            if ($row != null) {
                $count = $row[0];
            }
        }

        $stmt->free_result();
        $stmt->close();

        return $count;
    }

    /**
     * Prepare and execute statements and bind passed types and values.
     *
     * @param string $query
     * @param string $types
     * @param array $values
     * @return mysqli_stmt|false
     */
    private static function executeStatement($query, $types, array $values)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare($query);

        if (strlen($types) > 0 && count($values) > 0) {
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();

        return $stmt;
    }
}
