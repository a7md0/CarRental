<?php
require_once 'model.class.php';

class CarModel extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'car_model';
    protected static $primaryKeys = ['car_model_id'];
    protected static $autoIncrementKey = 'car_model_id';
    protected static $properties = ['car_model_id', 'car_type_id', 'brand', 'model', 'year'];

    /**
     * Undocumented function
     *
     * @param int $value
     * @return self
     */
    function setCarModelId($value)
    {
        parent::setValue('car_model_id', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    function getCarModelId()
    {
        return parent::getValue('car_model_id');
    }

    /**
     * Undocumented function
     *
     * @param int $value
     * @return self
     */
    function setCarTypeId($value)
    {
        parent::setValue('car_type_id', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    function getCarTypeId()
    {
        return parent::getValue('car_type_id');
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return self
     */
    function setBrand($value)
    {
        parent::setValue('brand', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    function getBrand()
    {
        return parent::getValue('brand');
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return self
     */
    function setModel($value)
    {
        parent::setValue('model', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    function getModel()
    {
        return parent::getValue('model');
    }

    /**
     * Undocumented function
     *
     * @param int $value
     * @return self
     */
    function setYear($value)
    {
        parent::setValue('year', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    function getYear()
    {
        return parent::getValue('year');
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
