<?php
require_once 'model.class.php';

class CarType extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'car_type';
    protected static $primaryKeys = ['car_type_id'];
    protected static $autoIncrementKey = 'car_type_id';
    protected static $properties = ['car_type_id', 'type'];

    /*
return parent::getValue('car_id');
parent::setValue('car_id', $value);
    */

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
     * @return self
     */
    public function setCarTypeId($value)
    {
        parent::setValue('car_type_id', $value);

        return $this;
    }

    /**
     * Get the value of type
     */
    public function getType()
    {
        return parent::getValue('type');
    }

    /**
     * Set the value of type
     *
     * @return self
     */
    public function setType($value)
    {
        parent::setValue('type', $value);

        return $this;
    }
}
