<?php
require_once('model.class.php');

class UserAddress extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'user_address';
    protected static $primaryKeys = ['user_address_id'];
    protected static $properties = ['user_address_id', 'user_id', 'type', 'address1', 'address2', 'country', 'city', 'zip_code'];

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
     * Get the value of user_address_id
     */
    public function getUserAddressId()
    {
        return parent::getValue('user_address_id');
    }

    /**
     * Set the value of user_address_id
     *
     * @return  self
     */
    public function setUserAddressId($value)
    {
        parent::setValue('user_address_id', $value);

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
     * Get the value of type
     */
    public function getType()
    {
        return parent::getValue('type');
    }

    /**
     * Set the value of type
     *
     * @return  self
     */
    public function setType($value)
    {
        parent::setValue('type', $value);

        return $this;
    }

    /**
     * Get the value of address1
     */
    public function getAddress1()
    {
        return parent::getValue('address1');
    }

    /**
     * Set the value of address1
     *
     * @return  self
     */
    public function setAddress1($value)
    {
        parent::setValue('address1', $value);

        return $this;
    }

    /**
     * Get the value of address2
     */
    public function getAddress2()
    {
        return parent::getValue('address2');
    }

    /**
     * Set the value of address2
     *
     * @return  self
     */
    public function setAddress2($value)
    {
        parent::setValue('address2', $value);

        return $this;
    }

    /**
     * Get the value of country
     */
    public function getCountry()
    {
        return parent::getValue('country');
    }

    /**
     * Set the value of country
     *
     * @return  self
     */
    public function setCountry($value)
    {
        parent::setValue('country', $value);

        return $this;
    }

    /**
     * Get the value of city
     */
    public function getCity()
    {
        return parent::getValue('city');
    }

    /**
     * Set the value of city
     *
     * @return  self
     */
    public function setCity($value)
    {
        parent::setValue('city', $value);

        return $this;
    }

    /**
     * Get the value of zip_code
     */
    public function getZipCode()
    {
        return parent::getValue('zip_code');
    }

    /**
     * Set the value of zip_code
     *
     * @return  self
     */
    public function setZipCode($value)
    {
        parent::setValue('zip_code', $value);

        return $this;
    }
}
