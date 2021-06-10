<?php
require_once 'model.class.php';

class CarModel extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'car_model';
    protected static $primaryKeys = ['car_model_id'];
    protected static $autoIncrementKey = 'car_model_id';
    protected static $properties = ['car_model_id', 'car_type_id', 'brand', 'model', 'year', 'number_of_seats'];

    /**
     * Get the value of car_model_id
     */
    public function getCarModelId()
    {
        return parent::getValue('car_model_id');
    }

    /**
     * Set the value of car_model_id
     *
     * @return  self
     */
    public function setCarModelId($value)
    {
        parent::setValue('car_model_id', $value);

        return $this;
    }

    /**
     * Get the value of car_type_id
     */
    public function getCarTypeId()
    {
        return parent::getValue('car_type_id');
    }

    /**
     * Set the value of car_type_id
     *
     * @return  self
     */
    public function setCarTypeId($value)
    {
        parent::setValue('car_type_id', $value);

        return $this;
    }

    /**
     * Get the value of brand
     */
    public function getBrand()
    {
        return parent::getValue('brand');
    }

    /**
     * Set the value of brand
     *
     * @return  self
     */
    public function setBrand($value)
    {
        parent::setValue('brand', $value);

        return $this;
    }

    /**
     * Get the value of model
     */
    public function getModel()
    {
        return parent::getValue('model');
    }

    /**
     * Set the value of model
     *
     * @return  self
     */
    public function setModel($value)
    {
        parent::setValue('model', $value);

        return $this;
    }

    /**
     * Get the value of year
     */
    public function getYear()
    {
        return parent::getValue('year');
    }

    /**
     * Set the value of year
     *
     * @return  self
     */
    public function setYear($value)
    {
        parent::setValue('year', $value);

        return $this;
    }

    /**
     * Get the value of number_of_seats
     */
    public function getNumberOfSeats()
    {
        return parent::getValue('number_of_seats');
    }

    /**
     * Set the value of number_of_seats
     *
     * @return  self
     */
    public function setNumberOfSeats($value)
    {
        parent::setValue('number_of_seats', $value);

        return $this;
    }

    function getFullDisplayName()
    {
        return $this->getBrand() . ' ' . $this->getModel() . ' (' . $this->getYear() . ')';
    }

    /**
     * Query the most popular reserved cars models
     *
     * @return array|null
     */
    public static function populateReservedModels()
    {

        $ucrTblName = UserCarReservation::getTableName();
        $carTblName = Car::getTableName();
        $carModelTblName = static::getTableName();

        $carPK = Car::primaryKeysColumns()[0]; // car_id
        $carModelPK = static::primaryKeysColumns()[0]; // car_model_id

        $query = "SELECT CM.*, COUNT(UCR.`$carPK`) AS times
                    FROM `$ucrTblName` UCR

                    INNER JOIN `$carTblName` AS C ON UCR.`$carPK` = C.`$carPK`
                    INNER JOIN `$carModelTblName` AS CM ON C.`$carModelPK` = CM.`$carModelPK`

                    GROUP BY C.`$carModelPK`
                    ORDER BY times DESC;";

        $stmt = Database::executeStatement($query, '', []);
        $models = [];

        if ($result = $stmt->get_result()) {
            while ($row = $result->fetch_assoc()) {
                $carModel = static::initializeFromData($row);

                $models[] = [$row['times'], $carModel];
            }
        }

        $stmt->free_result();
        $stmt->close();

        return $models;
    }
}
