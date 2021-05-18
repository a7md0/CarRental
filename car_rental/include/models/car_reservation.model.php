<?php
require_once('model.class.php');

class CarReservation extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'car_reservation';
    protected static $primaryKeys = ['user_car_reservation_id', 'car_id'];
    protected static $properties = ['user_car_reservation_id', 'car_id', 'pickup_date', 'return_date', 'status'];

    /*
return parent::getValue('car_id');
parent::setValue('car_id', $value);
    */

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
     * Get the value of user_car_reservation_id
     */
    public function getUserCarReservationId()
    {
        return parent::getValue('user_car_reservation_id');
    }

    /**
     * Set the value of user_car_reservation_id
     *
     * @return  self
     */
    public function setUserCarReservationId($value)
    {
        parent::setValue('user_car_reservation_id', $value);

        return $this;
    }

    /**
     * Get the value of car_id
     */
    public function getCarId()
    {
        return parent::getValue('car_id');
    }

    /**
     * Set the value of car_id
     *
     * @return  self
     */
    public function setCarId($value)
    {
        parent::setValue('car_id', $value);

        return $this;
    }

    /**
     * Get the value of pickup_date
     */
    public function getPickupDate()
    {
        return parent::getValue('pickup_date');
    }

    /**
     * Set the value of pickup_date
     *
     * @return  self
     */
    public function setPickupDate($value)
    {
        parent::setValue('pickup_date', $value);

        return $this;
    }

    /**
     * Get the value of return_date
     */
    public function getReturnDate()
    {
        return parent::getValue('return_date');
    }

    /**
     * Set the value of return_date
     *
     * @return  self
     */
    public function setReturnDate($value)
    {
        parent::setValue('return_date', $value);

        return $this;
    }

    /**
     * Get the value of status
     */
    public function getStatus()
    {
        return parent::getValue('status');
    }

    /**
     * Set the value of status
     *
     * @return  self
     */
    public function setStatus($value)
    {
        parent::setValue('status', $value);

        return $this;
    }
}