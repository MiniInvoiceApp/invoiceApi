<?php

class InvoiceController
{
    public function __construct()
    {
    }

    /**
     * Get function
     * Retrieve requested data
     *
     * @param $request
     */
    public function get($request)
    {
        $invoiceRepository = new InvoiceModel();
        $invoices = $invoiceRepository->getInvoices($request["limit"], $request["offset"]);
        $invoiceCount = $invoiceRepository->totalDataCount($request["id"]);

        echo json_encode(["data" => $invoices, "totalDataCount" => $invoiceCount]);
    }

    /**
     * Post function
     * Create new records
     *
     * @param $request
     */
    public function post($request)
    {
        $validation = new Validation();
        $validation->validate((array)$request, [
            "client" => "required|minLength=1|maxLength=255",
            "vatRate" => "required|numeric",
            "status" => "required|in=paid,unpaid",
            "invoiceItems" => "required|isArray|notEmptyArray"
        ]);

        if ($validation->hasErrors()) {
            http_response_code(400);
            echo json_encode(["errors" => $validation->getErrorMessages()]);
        } else {
            $invoiceModel = new InvoiceModel();
            $invoiceItemModel = new InvoiceItemModel();

            $invoiceDate = date("Y-m-d", time());

            //sum invoice amount
            $invoiceAmount = 0;
            foreach ($request->invoiceItems as $invoiceItem) {
                $invoiceAmount += $invoiceItem->amount;
            }

            //calculate invoice amount with vat
            $invoiceAmountWithVat = $invoiceAmount + ($invoiceAmount * $request->vatRate / 100);

            $invoiceModel->createNewInvoice($request->client, $invoiceAmount, $invoiceAmountWithVat, $request->vatRate, $request->status, $invoiceDate);
            $invoiceId = $invoiceModel->getLastInsertedInvoiceId();

            foreach ($request->invoiceItems as $invoiceItem) {
                $invoiceItemModel->createNewInvoiceItem($invoiceId, $invoiceItem->name, $invoiceItem->amount);
            }

            echo json_encode(["message" => "Invoice created successfully"]);
            http_response_code(201);
        }
    }

    /**
     * Patch function
     * Partial update a record
     *
     * @param $request
     * @param $id
     */
    public function patch($request, $id)
    {
        $validation = new Validation();
        $validation->validate((array)$request, [
            "client" => "minLength=1|maxLength=255",
            "vatRate" => "numeric",
            "invoice_status" => "in=paid,unpaid",
            "invoiceItems" => "isArray|notEmptyArray"
        ]);

        if ($validation->hasErrors()) {
            http_response_code(400);
            echo json_encode(["errors" => $validation->getErrorMessages()]);
        } else {
            $patchValues = "";
            foreach ($request as $key => $value) {
                $patchValues .= $key . " = '" . $value . "', ";
            }

            $invoiceRepository = new InvoiceModel();
            $invoiceRepository->patchInvoices(rtrim($patchValues, ", "), $id);
        }
    }

    /**
     * Delete function
     * Delete requested record from both invoice and invoice_items
     *
     * @param $request
     * @param $id
     */
    public function delete($request, $id)
    {
        $invoiceModel = new InvoiceModel();
        $invoiceItemModel = new InvoiceItemModel();

        $invoice = $invoiceModel->getInvoiceById($id);

        if (!empty($invoice)) {
            $invoiceModel->deleteInvoice($id);
            $invoiceItemModel->deleteByInvoiceId($id);
        } else {
            http_response_code(404);
        }
    }
}
