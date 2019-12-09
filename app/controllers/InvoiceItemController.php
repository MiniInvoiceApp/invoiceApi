<?php

class InvoiceItemController
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
        $invoiceItemRepository = new InvoiceItemModel();
        $invoiceItems = $invoiceItemRepository->getInvoiceItems($request["id"]);

        echo json_encode($invoiceItems);
    }
}
