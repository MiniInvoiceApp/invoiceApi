<?php
//Hide all errors from the client
error_reporting(E_ERROR | E_PARSE);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

date_default_timezone_set("UTC");
set_exception_handler("globalExceptionHandler");

require_once "autoload.php";

$baseUrl = getUri();
$route = explode("/", $baseUrl);

$requestMethod = strtolower(Request::method());
switch ($route[1]) {
    case "invoice":
        $invoice = new  InvoiceController();

        if (isset($route[2])) {
            $invoice->$requestMethod(Request::data(), $route[2]);
        } else {
            $invoice->$requestMethod(Request::data());
        }
        break;
    case "invoice-item":
        $invoiceItem = new InvoiceItemController();

        if (isset($route[2])) {
            $invoiceItem->$requestMethod(Request::data(), $route[2]);
        } else {
            $invoiceItem->$requestMethod(Request::data());
        }
        break;
    case "csv-export":
        $csvExport = new CsvExportController();

        $csvExport->$requestMethod(Request::data());
        break;
    default:
        http_response_code(404);
}

/**
 * Retrieve base uri in order to decide which route should we choose
 *
 * @return string
 */
function getUri()
{
    $basePath = implode("/", array_slice(explode("/", $_SERVER["SCRIPT_NAME"]), 0, -1)) . "/";

    $uri = substr($_SERVER["REQUEST_URI"], strlen($basePath));

    if (strstr($uri, "?")) {
        $uri = substr($uri, 0, strpos($uri, "?"));
    }

    $uri = "/" . trim($uri, "/");
    return $uri;
}

/**
 * Normally I would had a logger to log all errors from exceptions
 *
 * @param $exception
 */
function globalExceptionHandler($exception) {
    echo json_encode(["errors" => ["Exception:" . $exception->getMessage()]]);
    http_response_code(500);
}
