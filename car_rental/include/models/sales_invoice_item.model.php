<?php
require_once 'model.class.php';

class SalesInvoiceItem extends Model
{
    protected static $tableName = DB_TABLES_PREFIX . 'sales_invoice_item';
    protected static $primaryKeys = ['sales_invoice_item_id'];
    protected static $autoIncrementKey = 'sales_invoice_item_id';
    protected static $properties = ['sales_invoice_item_id', 'sales_invoice_id', 'item', 'price'];

    /*
return parent::getValue('car_id');
parent::setValue('car_id', $value);
    */

    /**
     * Get the value of sales_invoice_item_id
     */
    public function getSalesInvoiceItemId()
    {
        return parent::getValue('sales_invoice_item_id');
    }

    /**
     * Set the value of sales_invoice_item_id
     *
     * @return self
     */
    public function setSalesInvoiceItemId($value)
    {
        parent::setValue('sales_invoice_item_id', $value);

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
     * Get the value of item
     */
    public function getItem()
    {
        return parent::getValue('item');
    }

    /**
     * Set the value of item
     *
     * @return self
     */
    public function setItem($value)
    {
        parent::setValue('item', $value);

        return $this;
    }

    /**
     * Get the value of price
     */
    public function getPrice()
    {
        return parent::getValue('price');
    }

    /**
     * Set the value of price
     *
     * @return self
     */
    public function setPrice($value)
    {
        parent::setValue('price', $value);

        return $this;
    }
}
