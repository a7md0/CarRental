<?php
require_once 'model.class.php';

class Car extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'car';
    protected static $primaryKeys = ['car_id'];
    protected static $autoIncrementKey = 'car_id';
    protected static $properties = array('car_id', 'car_model_id', 'color', 'daily_rent_rate', 'license_plate', 'vehicle_identification_number', 'status', 'preview_image');

    /** @var CarModel */
    protected $carModel = null;

    /** @var CarType */
    protected $carType = null;

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

    /**
     * Get the value of carModel
     *
     * @return CarModel
     */
    public function getCarModel()
    {
        if ($this->carModel === null) {
            $this->carModel = CarModel::findById($this->getCarModelId());
        }

        return $this->carModel;
    }

    /**
     * Set the value of carModel
     *
     * @param CarModel $carModel
     * @return self
     */
    public function setCarModel($carModel)
    {
        $this->carModel = $carModel;

        return $this;
    }

    /**
     * Get the value of carType
     *
     * @return CarType
     */
    public function getCarType()
    {
        if ($this->carType === null) {
            $this->carType = CarType::findById($this->getCarModel()->getCarTypeId());
        }

        return $this->carType;
    }

    /**
     * Set the value of carType
     *
     * @param CarType $carType
     * @return self
     */
    public function setCarType($carType)
    {
        $this->carType = $carType;

        return $this;
    }

    /**
     * Check if car is reserved
     *
     * @param string $pickupDate
     * @param string $returnDate
     *
     * @return boolean
     */
    public function isReserved($pickupDate, $returnDate)
    {
        $car_id = $this->getCarId();
        $query = "SELECT is_car_reserved(?, ?, ?);";

        $stmt = Database::executeStatement($query, 'iss', [$car_id, $pickupDate, $returnDate]);

        $result = $stmt->get_result();
        $value = $result->fetch_row()[0] ?? 1;

        $stmt->free_result();
        $stmt->close();

        return $value == 0 ? false : true;
    }

    /**
     * Check if car is reserved (exclude for given reservation)
     *
     * @param int $reservationId
     * @param string $pickupDate
     * @param string $returnDate
     *
     * @return boolean
     */
    public function isReservedExcept($reservationId, $pickupDate, $returnDate)
    {
        $car_id = $this->getCarId();
        $query = "SELECT is_car_reserved_except(?, ?, ?, ?);";

        $stmt = Database::executeStatement($query, 'iiss', [$car_id, $reservationId, $pickupDate, $returnDate]);

        $result = $stmt->get_result();
        $value = $result->fetch_row()[0] ?? 1;

        $stmt->free_result();
        $stmt->close();

        return $value == 0 ? false : true;
    }
}
