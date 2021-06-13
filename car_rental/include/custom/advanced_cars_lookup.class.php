<?php

class AdvancedCarsLookup
{
    use Pagination;

    /** @var WhereClause */
    private $whereCarModel;
    /** @var WhereClause */
    private $whereCar;

    private $whereTypes = '';
    private $whereValues = [];
    public $query = '';

    function __construct()
    {
        $this->whereCarModel = new WhereClause('CM');
        $this->whereCar = new WhereClause('C');
    }

    private function buildQuery()
    {
        $this->whereTypes = '';
        $this->whereValues = [];
        $this->query = '';

        $carTblName = Car::getTableName();
        $carModelTblName = CarModel::getTableName();
        $carTypeTblName = CarType::getTableName();

        $onCarModelClause = '';
        if ($this->whereCarModel->hasAny()) {
            $onCarModelClause = ' ' . $this->whereCarModel->getSQL('AND');
            $this->whereTypes .= $this->whereCarModel->getTypes();
            $this->whereValues = array_merge($this->whereValues, $this->whereCarModel->getValues());
        }

        $whereCarClause = '';
        if ($this->whereCar->hasAny()) {
            $whereCarClause = $this->whereCar->getSQL();
            $this->whereTypes .= $this->whereCar->getTypes();
            $this->whereValues = array_merge($this->whereValues, $this->whereCar->getValues());
        }

        $this->query = " FROM `$carTblName` AS C

        INNER JOIN `$carModelTblName` AS CM
            ON C.`car_model_id` = CM.`car_model_id`$onCarModelClause

        INNER JOIN `$carTypeTblName` AS CT
            ON CM.`car_type_id` = CT.`car_type_id`

        $whereCarClause

         ORDER BY C.`car_id` ASC";
    }

    /**
     * Get the value of whereCarModel
     */
    public function carModelWhereClause()
    {
        return $this->whereCarModel;
    }

    /**
     * Get the value of whereCar
     */
    public function carWhereClause()
    {
        return $this->whereCar;
    }

    /**
     * Count matching rows and return count.
     *
     * @return int
     */
    public function count()
    {
        $this->buildQuery();
        $queryPrefix = 'SELECT COUNT(*)';
        $querySuffix = ';';

        $stmt = Database::executeStatement($queryPrefix . $this->query . $querySuffix, $this->whereTypes, $this->whereValues);
        $count = 0;

        if ($result = $stmt->get_result()) {
            $row = $result->fetch_row();

            if ($row !== null) {
                $count = $row[0];
            }
        }

        $stmt->free_result();
        $stmt->close();

        return $count;
    }

    /**
     * Find any matching records with the provided condition(s).
     *
     * @return Car[]
     */
    public function find()
    {
        $this->buildQuery();
        $queryPrefix = 'SELECT C.*, CM.*, CT.`type`';
        $querySuffix = $this->getLimitClause() . ';';

        $stmt = Database::executeStatement($queryPrefix . $this->query . $querySuffix, $this->whereTypes, $this->whereValues);
        $models = [];

        if ($result = $stmt->get_result()) {
            while ($row = $result->fetch_assoc()) {
                $car = Car::initializeFromData($row);
                $carModel = CarModel::initializeFromData($row);
                $carType = CarType::initializeFromData($row);

                $car->setCarModel($carModel);
                $car->setCarType($carType);

                $models[] = $car;
            }
        }

        $stmt->free_result();
        $stmt->close();

        return $models;
    }
}
