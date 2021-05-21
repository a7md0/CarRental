<?php
/*
 new WhereClause().where("x", "123", "=").whereBetween().or().where("")
*/
class WhereClause
{
    private $predicates = [new AndPredicate];
    public $values = [];

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

        $last->predicates[] = "`$column` $operator ?";
        $this->values[] = $value;

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

        $last->predicates[] = "`$column` " . $prefix . "BETWEEN ? AND ?";
        $this->values[] = $minValue;
        $this->values[] = $maxValue;

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

        $last->predicates[] = "`$column` IS " . $suffix . $v;

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
        }

        $prefix = $equal == true ? '' : 'NOT ';
        $list = join(", ", $placeholders);

        $last->predicates[] = "`$column` " . $prefix . "IN ($list)";

        return $this;
    }

    // TODO: Search by Full-Text index (array $cols, $queryStr, $searchMode = 'BOOLEAN')
    // TODO: Where col is equal col [for ON statements] ($prefix1, $col1, $prefix2, $col2, $operator = '=')
    // TODO: Set col prefix ($prefix)

    private function lastPredicate()
    {
        return $this->predicates[count($this->predicates) - 1];
    }
}

abstract class Predicate
{
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
