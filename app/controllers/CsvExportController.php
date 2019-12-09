<?php

class CsvExportController
{
    public function __construct()
    {
    }

    /**
     * Get function
     * Get request and call the proper function to gather data for the csv
     *
     * @param $request
     */
    public function get($request)
    {
        if (method_exists(self::class, $request["csv"])) {
            $functionToCall = $request["csv"];

            $this->$functionToCall();
        } else {
            http_response_code(404);
        }
    }

    /**
     * Gather data for the transactions csv
     */
    private function transactions()
    {
        $invoiceModel = new InvoiceModel();
        $transactions = $invoiceModel->getAllInvoices();

        $csvData = [];
        $csvData[] = ["Invoice ID", "Company Name", "Invoice Amount"];
        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                $csvData[] = [$transaction["id"], $transaction["client"], $transaction["invoice_amount_plus_vat"]];
            }
        }

        $this->getCsvFile($csvData, "transactions_" . time() . ".csv");
    }

    /**
     * Gather data for the customer report csv
     */
    private function customerReport()
    {
        $invoiceModel = new InvoiceModel();
        $customersReport = $invoiceModel->getAllInvoicesAmounts();

        $csvData = [];
        $csvData[] = ["Company Name", "Total Invoiced Amount", "Total Amount Paid", "Total Amount Outstanding"];
        if (!empty($customersReport)) {
            foreach ($customersReport as $customerReport) {
                $csvData[] = [$customerReport["client"], $customerReport["total_invoiced"], $customerReport["total_paid"], $customerReport["total_outstanding"]];
            }
        }

        $this->getCsvFile($csvData, "customer_report_" . time() . ".csv");
    }

    /**
     * Add proper headers and download the csv file
     *
     * @param $data
     * @param $fileName
     */
    private function getCsvFile($data, $fileName)
    {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen("php://output", "w");

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
    }
}
