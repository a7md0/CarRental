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
}
