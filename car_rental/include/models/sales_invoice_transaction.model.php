<?php
require_once('model.class.php');

class SalesInvoiceTransaction extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'sales_invoice_transaction';
    protected static $primaryKeys = ['sales_invoice_id', 'transaction_id'];
    protected static $properties = ['sales_invoice_id', 'transaction_id'];

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
     * Get the value of transaction_id
     */
    public function getTransactionId()
    {
        return parent::getValue('transaction_id');
    }

    /**
     * Set the value of transaction_id
     *
     * @return  self
     */
    public function setTransactionId($value)
    {
        parent::setValue('transaction_id', $value);

        return $this;
    }
}
