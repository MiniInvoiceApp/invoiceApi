<?php

class InvoiceModel
{
    private $db;

    public function __construct()
    {
        $this->db = MySQLiDriver::getInstance();
    }

    public function getInvoices($limit, $offset)
    {
        $sql = "SELECT * FROM invoices LIMIT $limit, $offset";
        $result = $this->db->query($sql);

        return $result->num_rows > 0 ? $result->rows : [];
    }

    public function getInvoiceById($id)
    {
        $sql = "SELECT * FROM invoices WHERE id = $id";
        $result = $this->db->query($sql);

        return $result->num_rows > 0 ? $result->rows[0] : [];
    }

    public function totalDataCount($id)
    {
        $sql = "SELECT count(*) AS count FROM invoices";
        $result = $this->db->query($sql);

        return $result->rows[0]["count"];
    }

    public function patchInvoices($values, $id)
    {
        $sql = "UPDATE invoices SET $values WHERE id = $id";
        $this->db->query($sql);
    }

    public function getAllInvoices()
    {
        $sql = "SELECT id, client, invoice_amount_plus_vat FROM invoices";
        $result = $this->db->query($sql);

        return $result->num_rows > 0 ? $result->rows : [];
    }

    public function getAllInvoicesAmounts()
    {
        $sql = "SELECT client, SUM(invoice_amount) as total_invoiced, 
                SUM(IF(invoice_status='paid', invoice_amount_plus_vat, 0)) as total_paid, 
                SUM(IF(invoice_status='unpaid', invoice_amount_plus_vat, 0)) as total_outstanding 
                FROM invoices 
                GROUP BY client";
        $result = $this->db->query($sql);

        return $result->num_rows > 0 ? $result->rows : [];
    }

    public function createNewInvoice($client, $invoiceAmount, $invoiceAmountWithVat, $vat, $status, $invoiceDate)
    {
        $client = $this->db->escape($client);

        $sql = "INSERT INTO invoices (client, invoice_amount, invoice_amount_plus_vat, vat_rate, invoice_status, invoice_date, created_at) 
                VALUES ('$client', $invoiceAmount, $invoiceAmountWithVat, $vat, '$status', '$invoiceDate', NOW())";
        $this->db->query($sql);
    }

    public function getLastInsertedInvoiceId()
    {
        return $this->db->getLastId();
    }

    public function deleteInvoice($id)
    {
        $sql = "DELETE FROM invoices WHERE id = $id";
        $this->db->query($sql);
    }
}
