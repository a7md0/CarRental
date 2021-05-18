<?php
require_once('model.class.php');

class Car extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'car';
    protected static $primaryKeys = ['car_id'];
    protected static $properties = array('car_id', 'car_model_id', 'color', 'daily_rent_rate', 'license_plate', 'vehicle_identification_number', 'status', 'preview_image');


    /**
     * Undocumented function
     *
     * @param array $data
     * @return self
     */
    static function createFromDb(array $data)
    {
        $model = new self;

        foreach ($data as $key => $value) {
            $model->values[$key] = $value;
        }

        return $model;
    }

    /**
     * Undocumented function
     *
     * @param int $value
     * @return self
     */
    function setCarId($value)
    {
        parent::setValue('car_id', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    function getCarId()
    {
        return parent::getValue('car_id');
    }

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
     * @param string $value
     * @return self
     */
    function setColor($value)
    {
        parent::setValue('color', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    function getColor()
    {
        return parent::getValue('color');
    }

    /**
     * Undocumented function
     *
     * @param float $value
     * @return self
     */
    function setDailyRentRate($value)
    {
        parent::setValue('daily_rent_rate', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return float
     */
    function getDailyRentRate()
    {
        return parent::getValue('daily_rent_rate');
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return self
     */
    function setLicensePlate($value)
    {
        parent::setValue('license_plate', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    function getLicensePlate()
    {
        return parent::getValue('license_plate');
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return self
     */
    function setVehicleIdentificationNumber($value)
    {
        parent::setValue('vehicle_identification_number', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    function getVehicleIdentificationNumber()
    {
        return parent::getValue('vehicle_identification_number');
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return self
     */
    function setStatus($value)
    {
        parent::setValue('status', $value);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    function getStatus()
    {
        return parent::getValue('status');
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
}
