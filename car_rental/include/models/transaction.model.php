<?php
require_once('model.class.php');

class Transaction extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'transaction';
    protected static $primaryKeys = ['transaction_id'];
    protected static $properties = ['transaction_id', 'sales_invoice_id', 'amount', 'method', 'remark', 'created_at'];

    /*
return parent::getValue('car_id');
parent::setValue('car_id', $value);
    */

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
     * @return self
     */
    public function setTransaction_id($value)
    {
        parent::setValue('transaction_id', $value);

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
     * Get the value of amount
     */
    public function getAmount()
    {
        return parent::getValue('amount');
    }

    /**
     * Set the value of amount
     *
     * @return self
     */
    public function setAmount($value)
    {
        parent::setValue('amount', $value);

        return $this;
    }

    /**
     * Get the value of method
     */
    public function getMethod()
    {
        return parent::getValue('method');
    }

    /**
     * Set the value of method
     *
     * @return self
     */
    public function setMethod($value)
    {
        parent::setValue('method', $value);

        return $this;
    }

    /**
     * Get the value of remark
     */
    public function getRemark()
    {
        return parent::getValue('remark');
    }

    /**
     * Set the value of remark
     *
     * @return self
     */
    public function setRemark($value)
    {
        parent::setValue('remark', $value);

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
}
