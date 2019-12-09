<?php

class InvoiceItemModel
{
    private $db;

    public function __construct()
    {
        $this->db = MySQLiDriver::getInstance();
    }

    public function getInvoiceItems($id)
    {
        $sql = "SELECT * FROM invoice_items WHERE invoice_id = $id";
        $result = $this->db->query($sql);

        return $result->num_rows > 0 ? $result->rows : [];
    }

    public function createNewInvoiceItem($invoiceId, $invoiceName, $amount)
    {
        $sql = "INSERT INTO invoice_items (invoice_id, name, amount, created_at) 
                VALUES ($invoiceId, '$invoiceName', $amount, NOW())";
        $this->db->query($sql);
    }

    public function deleteByInvoiceId($invoiceId)
    {
        $sql = "DELETE FROM invoice_items WHERE invoice_id = $invoiceId";
        $this->db->query($sql);
    }
}
