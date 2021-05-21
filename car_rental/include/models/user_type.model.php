<?php
require_once('model.class.php');

class UserType extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'user_type';
    protected static $primaryKeys = ['user_type_id'];
    protected static $properties = ['user_type_id', 'type', 'access_level'];

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
     * Get the value of access_level
     */
    public function getAccessLevel()
    {
        return parent::getValue('access_level');
    }

    /**
     * Set the value of access_level
     *
     * @return  self
     */
    public function setAccessLevel($value)
    {
        parent::setValue('access_level', $value);

        return $this;
    }
}
