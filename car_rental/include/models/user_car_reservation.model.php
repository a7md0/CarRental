<?php
require_once('model.class.php');

class UserCarReservation extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'user_car_reservation';
    protected static $primaryKeys = ['user_car_reservation_id'];
    protected static $properties = ['user_car_reservation_id', 'user_id', 'car_id', 'pickup_date', 'return_date', 'sales_invoice_id', 'status', 'created_at', 'updated_at'];

    /*
return parent::getValue('car_id');
parent::setValue('car_id', $value);
    */

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
     * Get the value of user_id
     */
    public function getUserId()
    {
        return parent::getValue('user_id');
    }

    /**
     * Set the value of user_id
     *
     * @return  self
     */
    public function setUserId($value)
    {
        parent::setValue('user_id', $value);

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
     * Get the value of sales_invoice_id
     */
    public function getSalesInvoiceId()
    {
        return parent::getValue('sales_invoice_id');
    }

    /**
     * Set the value of sales_invoice_id
     *
     * @return  self
     */
    public function setSalesInvoiceId($value)
    {
        parent::setValue('sales_invoice_id', $value);

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

    /**
     * Get the value of created_at
     */
    public function getCreatedAt()
    {
        return parent::getValue('created_at');
    }

    /**
     * Set the value of created_at
     *
     * @return  self
     */
    public function setCreatedAt($value)
    {
        parent::setValue('created_at', $value);

        return $this;
    }

    /**
     * Get the value of updated_at
     */
    public function getUpdatedAt()
    {
        return parent::getValue('updated_at');
    }

    /**
     * Set the value of updated_at
     *
     * @return  self
     */
    public function setUpdatedAt($value)
    {
        parent::setValue('updated_at', $value);

        return $this;
    }
}
