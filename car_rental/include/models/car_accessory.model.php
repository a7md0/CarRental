<?php
require_once 'model.class.php';

class CarAccessory extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'car_accessory';
    protected static $primaryKeys = ['car_accessory_id'];
    protected static $autoIncrementKey = 'car_accessory_id';
    protected static $properties = ['car_accessory_id', 'car_type_id', 'name', 'preview_image', 'charge'];

    /**
     * Undocumented function
     *
     * @param int $value
     * @return self
     */
    function setCarAccessoryId($value)
    {
        parent::setValue('car_accessory_id', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    function getCarAccessoryId()
    {
        return parent::getValue('car_accessory_id');
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
    function setName($value)
    {
        parent::setValue('name', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    function getName()
    {
        return parent::getValue('name');
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return self
     */
    function setPreviewImage($value)
    {
        parent::setValue('preview_image', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    function getPreviewImage()
    {
        return parent::getValue('preview_image');
    }

    /**
     * Undocumented function
     *
     * @param float $value
     * @return self
     */
    function setCharge($value)
    {
        parent::setValue('charge', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return float
     */
    function getCharge()
    {
        return parent::getValue('charge');
    }
}
