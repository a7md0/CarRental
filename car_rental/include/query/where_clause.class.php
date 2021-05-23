<?php
/*
 new WhereClause().where("x", "123", "=").whereBetween().or().where("")
*/
class WhereClause
{
    /** @var Predicate[] */
    private $predicates = [];
    private $values = [];
    private $types = [];

    private $columnPrefix;

    public function __construct($columnPrefix = null)
    {
        $this->predicates[] = new AndPredicate;
        $this->columnPrefix = $columnPrefix;
    }

    public function hasAny()
    {
        return count($this->predicates) > 1 || $this->predicates[0]->hasAny();
    }

    public function or()
    {
        $this->predicates[] = new OrPredicate;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $column
     * @param string|int|float $value
     * @param string $operator
     * @return self
     */
    public function where($column, $value, $operator = '=')
    {
        $last = $this->lastPredicate();

        $col = $this->parseColumn($column);
        $last->predicates[] = "$col $operator ?";
        $this->values[] = $value;
        $this->types[] = $this->typeOfValue($value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $column
     * @param int|float|string $minValue
     * @param int|float|string $maxValue
     * @param boolean $equal
     * @return self
     */
    public function whereBetween($column, $minValue, $maxValue, $equal = true)
    {
        $last = $this->lastPredicate();

        $prefix = $equal == true ? '' : 'NOT ';
        $col = $this->parseColumn($column);

        $last->predicates[] = "$col " . $prefix . "BETWEEN ? AND ?";

        $this->values[] = $minValue;
        $this->types[] = $this->typeOfValue($minValue);

        $this->values[] = $maxValue;
        $this->types[] = $this->typeOfValue($maxValue);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $column
     * @param boolean|null $value
     * @param boolean $equal
     * @return self
     */
    public function whereIs($column, $value, $equal = true)
    {
        $last = $this->lastPredicate();

        $suffix = $equal == true ? '' : 'NOT ';
        $v = $value == null ? 'NULL' : ($value === true ? 'TRUE' : 'FALSE');
        $col = $this->parseColumn($column);

        $last->predicates[] = "$col IS " . $suffix . $v;

        return $this;
    }

    /*public function whereDate($column, $value, $equal = true) {

        return $this;
    }

    public function whereLike($column, $value, $equal = true) {

        return $this;
    }*/

    /**
     * Undocumented function
     *
     * @param string $column
     * @param array $values
     * @param boolean $equal
     * @return self
     */
    public function whereIn($column, array $values, $equal = true)
    {
        $last = $this->lastPredicate();
        $placeholders = [];

        foreach ($values as $value) {
            $placeholders[] = "?";
            $this->values[] = $value;
            $this->types[] = $this->typeOfValue($value);
        }

        $prefix = $equal == true ? '' : 'NOT ';
        $list = join(", ", $placeholders);
        $col = $this->parseColumn($column);

        $last->predicates[] = "$col " . $prefix . "IN ($list)";

        return $this;
    }

    public function whereFullText(array $columns, $query, $mode = 'NATURAL LANGUAGE')
    {
        $last = $this->lastPredicate();

        $cols = array_map(function($column) {
            return $this->parseColumn($column);
        }, $columns);

        $last->predicates[] = "MATCH (" . join(', ', $cols)  . ") AGAINST (? IN $mode MODE)";
        $this->values[] = $query;
        $this->types[] = $this->typeOfValue($query);

        return $this;
    }

    public function whereColumn($column, $column2Prefix, $column2 = $column, $operator = '=')
    {
        $last = $this->lastPredicate();
        $col = $this->parseColumn($column);

        $last->predicates[] = "$col $operator $column2Prefix.`$column2`";

        return $this;
    }

    // TODO: Set col prefix ($prefix)

    /**
     * Return built string part of the where clause.
     *
     * @param string $defaultClause The default clause to use, default to 'WHERE'
     * @return string
     */
    public function getSQL($defaultClause = 'WHERE')
    {
        $clause = '';

        foreach ($this->predicates as $predicate) {
            if ($predicate->hasAny()) {
                $predicates = $predicate->predicates;

                if (strlen($clause) > 0) {
                    $op = $predicate->predicateOperator(); // "AND" or "OR"
                    $clause .= " $op";
                }

                $clause .= join(" AND ", $predicates);
            }
        }

        return strlen($clause) > 0 ? "$defaultClause $clause" : "";
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

    /**
     * Get the value of columnPrefix
     */
    public function getColumnPrefix()
    {
        return $this->columnPrefix;
    }

    private function parseColumn($column) {
        if ($this->columnPrefix == null) {
            return "`$column`";
        }

        return "$this->columnPrefix.`$column`";
    }

    private function lastPredicate()
    {
        return $this->predicates[count($this->predicates) - 1];
    }

    private function typeOfValue($value)
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

    public function __clone()
    {
        $this->predicates = clone $this->predicates;
        $this->values = clone $this->values;
        $this->types = clone $this->types;
    }
}

abstract class Predicate
{
    /** @var string[] */
    public $predicates = [];

    abstract function predicateOperator();

    public function hasAny()
    {
        return count($this->predicates) > 0;
    }
}

class AndPredicate extends Predicate
{
    function predicateOperator()
    {
        return 'AND';
    }
}

class OrPredicate extends Predicate
{
    function predicateOperator()
    {
        return 'OR';
    }
}
