<?php

class InsertClause
{
    /** @var string[] */
    private $columns = [];
    /** @var string[] */
    private $valuesPlaceholder = [];

    /** @var mixed[] */
    private $values = [];
    /** @var string[] */
    private $types = [];

    public function __construct(array $data, $autoIncrementKey)
    {
        foreach ($data as $column => $value) {
            if ($column == $autoIncrementKey) {
                continue;
            }

            if ($value === null) {
                continue;
            }

            $this->columns[] = $column;
            $this->valuesPlaceholder[] = '?';

            $this->values[] = $value;
            $this->types[] = static::typeOfValue($value);
        }
    }

    public function hasAny()
    {
        return count($this->predicates) > 0;
    }

    /**
     * Return built string part of the where clause.
     *
     * @return string
     */
    public function getSQL()
    {
        return '(' . join(', ', $this->columns) . ') VALUES (' .  join(', ', $this->valuesPlaceholder) . ')';
    }

    /**
     * Return built string part of the where clause.
     *
     * @return string
     */
    public function getSqlValues()
    {
        return '(' .  join(', ', $this->valuesPlaceholder) . ')';
    }

    /**
     * Return string representation of current types in one string.
     *
     * @return string
     */
    public function getTypes()
    {
        return join('', $this->types);
    }

    /**
     * Return values of the current where clause.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    private static function typeOfValue($value)
    {
        switch (gettype($value)) {
            case 'integer':
                return 'i';
            case 'double':
                return 'd';
            case 'string':
                return 's';
            default:
                return 'b';
        }
    }
}
