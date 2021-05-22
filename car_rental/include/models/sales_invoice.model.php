<?php
require_once 'model.class.php';

class SalesInvoice extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'sales_invoice';
    protected static $primaryKeys = ['sales_invoice_id'];
    protected static $autoIncrementKey = 'sales_invoice_id';
    protected static $properties = ['sales_invoice_id', 'status', 'paid_amount', 'grand_total', 'ref_id'];

    /*
return parent::getValue('car_id');
parent::setValue('car_id', $value);
    */

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
     * Get the value of paid_amount
     */
    public function getPaidAmount()
    {
        return parent::getValue('paid_amount');
    }

    /**
     * Set the value of paid_amount
     *
     * @return self
     */
    public function setPaidAmount($value)
    {
        parent::setValue('paid_amount', $value);

        return $this;
    }

    /**
     * Get the value of grand_total
     */
    public function getGrandTotal()
    {
        return parent::getValue('grand_total');
    }

    /**
     * Set the value of grand_total
     *
     * @return self
     */
    public function setGrandTotal($value)
    {
        parent::setValue('grand_total', $value);

        return $this;
    }

    /**
     * Get the value of ref_id
     */
    public function getRefId()
    {
        return parent::getValue('ref_id');
    }

    /**
     * Set the value of ref_id
     *
     * @return self
     */
    public function setRefId($value)
    {
        parent::setValue('ref_id', $value);

        return $this;
    }
}
