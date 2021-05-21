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
     * @return static
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
        if (!in_array($columnName, static::$properties)) {
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
     * Get all values.
     *
     * @param string $columnName
     * @return array
     */
    function getValues()
    {
        return $this->values;
    }

    public static function primaryKeysColumns() {
        return static::$primaryKeys;
    }

    function insert()
    {
        $affectedRows = static::insertMany([$this]);

        return $affectedRows == 1;
    }

    function update()
    {
        $primaryKeys = static::$primaryKeys;

        $setClause = new SetClause($this->values, static::primaryKeysColumns());
        $whereClause = new WhereClause();

        foreach ($primaryKeys as $primaryKey) {
            $whereClause->where($primaryKey, $this->values[$primaryKey]);
        }

        $affectedRows = static::updateMany($setClause, $whereClause, 1);

        return $affectedRows == 1;
    }

    function delete()
    {
        $primaryKeys = static::$primaryKeys;
        $whereClause = new WhereClause();

        foreach ($primaryKeys as $primaryKey) {
            $whereClause->where($primaryKey, $this->values[$primaryKey]);
        }

        $affectedRows = static::deleteMany($whereClause, 1);

        return $affectedRows == 1;
    }

    /**
     * Undocumented function
     *
     * @param Model[] $models
     * @return int
     */
    static function insertMany(array $models)
    {
        $tblName = static::$tableName;
        $queries = '';
        $insertTypes = '';
        $insertValues = [];

        foreach ($models as $model) {
            $insert = new InsertClause($model->getValues(), $model::primaryKeysColumns());
            $insertClause = $insert->getSQL();
            $insertTypes .= $insert->getTypes();
            $insertValues += $insert->getValues();

            $queries .= "INSERT INTO `$tblName`$insertClause; ";
        }

        $stmt = static::executeStatement($queries, $insertTypes, $insertValues);
        $affectedRows = $stmt->affected_rows;

        $stmt->free_result();
        $stmt->close();

        return $affectedRows;
    }

    static function updateMany(SetClause $set, WhereClause $where = null, $limit = null)
    {
        $tblName = static::$tableName;

        $setClause = '';
        $setTypes = '';
        $setValues = [];

        if (isset($set) && $set->hasAny()) {
            $setClause = ' ' . $set->getSQL() . ' ';
            $setTypes = $set->getTypes();
            $setValues = $set->getValues();
        }

        $whereClause = '';
        $whereTypes = '';
        $whereValues = [];

        $limitClause = $limit == null ? '' : " LIMIT $limit";

        if (isset($where) && $where->hasAny()) {
            $whereClause = ' ' . $where->getSQL();
            $whereTypes = $where->getTypes();
            $whereValues = $where->getValues();
        }

        $query = "UPDATE `$tblName`$setClause$whereClause$limitClause;"; // TODO: Set clause

        $stmt = static::executeStatement($query, $setTypes . $whereTypes, array_merge($setValues, $whereValues));
        $affectedRows = $stmt->affected_rows;

        $stmt->free_result();
        $stmt->close();

        return $affectedRows;
    }

    /**
     * Delete many records from the current table where the provided condition(s) matches. Also, it is possible to limit the delete process to number of rows.
     *
     * @param WhereClause $where
     * @param int|null $limit
     * @return int
     */
    static function deleteMany(WhereClause $where = null, $limit = null)
    {
        $tblName = static::$tableName;

        $whereClause = '';
        $whereTypes = '';
        $whereValues = [];

        $limitClause = $limit == null ? '' : " LIMIT $limit";

        if (isset($where) && $where->hasAny()) {
            $whereClause = ' ' . $where->getSQL();
            $whereTypes = $where->getTypes();
            $whereValues = $where->getValues();
        }

        $query = "DELETE FROM `$tblName`$whereClause$limitClause;"; // TODO: Set clause

        $stmt = static::executeStatement($query, $whereTypes, $whereValues);
        $affectedRows = $stmt->affected_rows;

        $stmt->free_result();
        $stmt->close();

        return $affectedRows;
    }

    /**
     * Initialize new object from the provided data.
     *
     * @param array $data
     * @return static
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
     * @return static|null
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
     * @return static|null
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
     * @return static|null
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
