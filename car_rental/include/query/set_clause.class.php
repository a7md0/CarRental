<?php

class SetClause
{
    /** @var string[] */
    private $predicates = [];
    /** @var mixed[] */
    private $values = [];
    /** @var string[] */
    private $types = [];

    public function __construct(array $data, array $primaryKeys)
    {
        foreach ($data as $column => $value) {
            if (in_array($column, $primaryKeys)) {
                continue;
            }

            $val = $value;

            if ($val == null) {
                $val = 'NULL';
            }

            $this->predicates[] = "`$column` = ?";
            $this->values[] = $val;
            $this->types[] = static::typeOfValue($val);
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
        return 'SET ' . join(', ', $this->predicates);
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
