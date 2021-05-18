<?php
require_once('model.class.php');

class User extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'user';
    protected static $primaryKeys = ['user_id'];
    protected static $properties = ['user_id', 'user_type_id', 'first_name', 'last_name', 'email', 'password', 'cpr', 'nationality'];

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
     * Get the value of user_type_id
     */
    public function getUserTypeId()
    {
        return parent::getValue('user_type_id');
    }

    /**
     * Set the value of user_type_id
     *
     * @return  self
     */
    public function setUserTypeId($value)
    {
        parent::setValue('user_type_id', $value);

        return $this;
    }

    /**
     * Get the value of first_name
     */
    public function getFirstName()
    {
        return parent::getValue('first_name');
    }

    /**
     * Set the value of first_name
     *
     * @return  self
     */
    public function setFirstName($value)
    {
        parent::setValue('first_name', $value);

        return $this;
    }

    /**
     * Get the value of last_name
     */
    public function getLastName()
    {
        return parent::getValue('last_name');
    }

    /**
     * Set the value of last_name
     *
     * @return  self
     */
    public function setLastName($value)
    {
        parent::setValue('last_name', $value);

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return parent::getValue('email');
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($value)
    {
        parent::setValue('email', $value);

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return parent::getValue('password');
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($value)
    {
        parent::setValue('password', $value);

        return $this;
    }

    /**
     * Get the value of cpr
     */
    public function getCpr()
    {
        return parent::getValue('cpr');
    }

    /**
     * Set the value of cpr
     *
     * @return  self
     */
    public function setCpr($value)
    {
        parent::setValue('cpr', $value);

        return $this;
    }

    /**
     * Get the value of nationality
     */
    public function getNationality()
    {
        return parent::getValue('nationality');
    }

    /**
     * Set the value of nationality
     *
     * @return  self
     */
    public function setNationality($value)
    {
        parent::setValue('nationality', $value);

        return $this;
    }
}
