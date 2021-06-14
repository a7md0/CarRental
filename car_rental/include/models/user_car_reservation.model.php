<?php
require_once 'model.class.php';

class UserCarReservation extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'user_car_reservation';
    protected static $primaryKeys = ['user_car_reservation_id'];
    protected static $autoIncrementKey = 'user_car_reservation_id';
    protected static $properties = ['user_car_reservation_id', 'user_id', 'car_id', 'reservation_code', 'pickup_date', 'return_date', 'sales_invoice_id', 'status', 'is_amended', 'created_at', 'updated_at'];

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
     * @return self
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
     * @return self
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
     * @return self
     */
    public function setCarId($value)
    {
        parent::setValue('car_id', $value);

        return $this;
    }

    /**
     * Get the value of reservation_code
     */
    public function getReservationCode()
    {
        return parent::getValue('reservation_code');
    }

    /**
     * Set the value of reservation_code
     *
     * @return self
     */
    public function setReservationCode($value)
    {
        parent::setValue('reservation_code', $value);

        return $this;
    }

    /**
     * Get the value of pickup_date
     *
     * @return string
     */
    public function getPickupDate()
    {
        return parent::getValue('pickup_date');
    }

    /**
     * Set the value of pickup_date
     *
     * @return self
     */
    public function setPickupDate($value)
    {
        parent::setValue('pickup_date', $value);

        return $this;
    }

    /**
     * Get the value of return_date
     *
     * @return string
     */
    public function getReturnDate()
    {
        return parent::getValue('return_date');
    }

    /**
     * Set the value of return_date
     *
     * @return self
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
     * @return self
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
     * @return self
     */
    public function setStatus($value)
    {
        parent::setValue('status', $value);

        return $this;
    }

    /**
     * Get the value of is_amended
     */
    public function getIsAmended()
    {
        return parent::getValue('is_amended');
    }

    /**
     * Set the value of is_amended
     *
     * @return self
     */
    public function setIsAmended($value)
    {
        parent::setValue('is_amended', $value);

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
     * @return self
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
     * @return self
     */
    public function setUpdatedAt($value)
    {
        parent::setValue('updated_at', $value);

        return $this;
    }

    /**
     * Indicate whether the customer can amend the reservation or not.
     *
     * @param string $why Output for why the customer cannot amend
     * @return boolean
     */
    public function canAmend(&$why)
    {
        if ($this->getIsAmended() == true) {
            $why = 'Already amended before';
            return false;
        }

        if ($this->getStatus() == 'cancelled') {
            $why = 'Already cancelled';
            return false;
        }

        $now = new DateTime();
        $pickupDate = date_create($this->getPickupDate());

        if ($pickupDate <= $now) {
            $why = 'Pickup date is already passed';
            return false;
        }

        $remainingDays = $pickupDate->diff($now)->days + 1;

        if ($remainingDays <= 2) {
            $why = 'Pickup date is due in two or less days';
            return false;
        }

        return true;
    }

    /**
     * Indicate whether the customer can cancel the reservation or not.
     *
     * @return boolean
     */
    public function canCancel(&$why)
    {
        if ($this->getStatus() == 'cancelled') {
            $why = 'Already cancelled';
            return false;
        }

        $now = new DateTime();
        $pickupDate = date_create($this->getPickupDate());

        if ($pickupDate <= $now) {
            $why = 'Pickup date already passed';
            return false;
        }

        return true;
    }

    /**
     * Amend reservation
     *
     * @param string $pickupDate
     * @param string $returnDate
     */
    public function amend($pickupDate, $returnDate, &$error = null)
    {
        $ucrTblName = UserCarReservation::getTableName();
        $carTblName = Car::getTableName();
        $siTblName = SalesInvoice::getTableName();

        $query = "SELECT UCR.`car_id`, UCR.`sales_invoice_id`, C.`daily_rent_rate`, SI.`grand_total`

                        FROM `$ucrTblName` AS UCR

                        INNER JOIN `$carTblName` AS C
                            ON UCR.`car_id` = C.`car_id`

                        INNER JOIN `$siTblName` AS SI
                            ON UCR.`sales_invoice_id` = SI.`sales_invoice_id`

                        WHERE UCR.`user_car_reservation_id` = ?;";

        $stmt = Database::executeStatement($query, 's', [$this->getUserCarReservationId()]);

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        $stmt->free_result();
        $stmt->close();

        $car = (new Car())->setCarId($data['car_id']);
        if ($car->isReservedExcept($this->getUserCarReservationId(), $pickupDate, $returnDate)) {
            $error = "Car is not available between $pickupDate and $returnDate";
            return;
        }

        $pickupDateObj = date_create($pickupDate);
        $returnDateObj = date_create($returnDate);

        $reservationDays = $pickupDateObj->diff($returnDateObj)->days + 1;

        Database::getInstance()->query( "CALL apply_amend_fees(" . $this->getSalesInvoiceId() . ");");
        Database::getInstance()->closeConnection();

        $query = "UPDATE `dbproj_user_car_reservation` AS UCR
                    INNER JOIN `dbproj_sales_invoice_item` AS SII
                        ON UCR.`sales_invoice_id` = SII.`sales_invoice_id` AND SII.`item` = 'Car rent'
                    SET UCR.`pickup_date` = ?, UCR.`return_date` = ?, UCR.`is_amended` = true, SII.`price` = (? * ?)
                    WHERE UCR.`user_car_reservation_id` = ?;";
        $stmt = Database::executeStatement($query, 'ssids', [$pickupDate, $returnDate, $reservationDays, $data['daily_rent_rate'], $this->getUserCarReservationId()]);

        $stmt->free_result();
        $stmt->close();

        $this->updateInvoice();
    }

    private function updateInvoice()
    {
        Database::getInstance()->query("CALL update_sales_invoice(" . $this->getSalesInvoiceId() . ");");
        Database::getInstance()->closeConnection();
    }

    /**
     * Cancel reservation
     *
     */
    public function cancel(&$error = null)
    {
        Database::getInstance()->query("CALL cancel_reservation(" . $this->getUserCarReservationId() . ");");
        Database::getInstance()->closeConnection();
    }
}
