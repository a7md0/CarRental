<?php
require_once('model.class.php');

class CarReservationAccessory extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'car_reservation_accessory';
    protected static $primaryKeys = ['car_id', 'user_car_reservation_id', 'car_accessory_id'];
    protected static $properties = ['car_id', 'user_car_reservation_id', 'car_accessory_id'];

    /*
return parent::getValue('car_id');
parent::setValue('car_id', $value);
    */

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
     * @return self
     */
    public function setCarId($value)
    {
        parent::setValue('car_id', $value);

        return $this;
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
     * @return self
     */
    public function setUserCarReservationId($value)
    {
        parent::setValue('user_car_reservation_id', $value);

        return $this;
    }

    /**
     * Get the value of car_accessory_id
     */
    public function getCarAccessoryId()
    {
        return parent::getValue('car_accessory_id');
    }

    /**
     * Set the value of car_accessory_id
     *
     * @return self
     */
    public function setCarAccessoryId($value)
    {
        parent::setValue('car_accessory_id', $value);

        return $this;
    }
}
