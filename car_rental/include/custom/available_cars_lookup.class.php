<?php

class AvailableCarsLookup
{
    use Pagination;

    /** @var WhereClause */
    private $whereCarModel;
    /** @var WhereClause */
    private $whereCar;

    private $pickupDate;
    private $returnDate;

    private $whereTypes = '';
    private $whereValues = [];
    public $query = '';

    function __construct($pickupDate, $returnDate)
    {
        $this->whereCarModel = new WhereClause('CM');
        $this->whereCar = new WhereClause('C');

        $this->pickupDate = $pickupDate;
        $this->returnDate = $returnDate;
    }

    private function buildQuery()
    {
        $this->whereTypes = '';
        $this->whereValues = [];
        $this->query = '';

        $carTblName = Car::getTableName();
        $userCarReservationTblName = UserCarReservation::getTableName();
        $carModelTblName = CarModel::getTableName();

        $onUserCarReservation = new WhereClause('UCR');
        $onUserCarReservation->whereColumn('car_id', $this->whereCar->getColumnPrefix(), 'car_id')
            ->where('status', 'confirmed')
            ->where('return_date', $this->pickupDate, '>=')
            ->where('pickup_date', $this->returnDate, '<=');

        $onUserCarReservationClause = $onUserCarReservation->getSQL('ON');
        $this->whereTypes .= $onUserCarReservation->getTypes();
        $this->whereValues = array_merge($this->whereValues, $onUserCarReservation->getValues());

        $onCarModelClause = '';
        if ($this->whereCarModel->hasAny()) {
            $onCarModelClause = ' ' . $this->whereCarModel->getSQL('AND');
            $this->whereTypes .= $this->whereCarModel->getTypes();
            $this->whereValues = array_merge($this->whereValues, $this->whereCarModel->getValues());
        }

        $whereCarClause = '';
        if ($this->whereCar->hasAny()) {
            $whereCarClause = ' ' . $this->whereCar->getSQL('AND');
            $this->whereTypes .= $this->whereCar->getTypes();
            $this->whereValues = array_merge($this->whereValues, $this->whereCar->getValues());
        }

        $this->query = " FROM `$carTblName` AS C

        LEFT JOIN `$userCarReservationTblName` AS UCR
            $onUserCarReservationClause

        INNER JOIN `$carModelTblName` AS CM
            ON C.`car_model_id` = CM.`car_model_id`$onCarModelClause

        WHERE C.`status` = 'available'
            AND UCR.`car_id` IS NULL$whereCarClause";
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

            if ($row != null) {
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
        $queryPrefix = 'SELECT C.*, CM.*';
        $querySuffix = $this->getLimitClause() . ';';

        $stmt = Database::executeStatement($queryPrefix . $this->query . $querySuffix, $this->whereTypes, $this->whereValues);
        $models = [];

        if ($result = $stmt->get_result()) {
            while ($row = $result->fetch_assoc()) {
                $car = Car::initializeFromData($row);
                $carModel = CarModel::initializeFromData($row);

                $car->setCarModel($carModel);

                $models[] = $car;
            }
        }

        $stmt->free_result();
        $stmt->close();

        return $models;
    }
}
