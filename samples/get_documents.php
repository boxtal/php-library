<?php
use \Emc\OrderStatus;

/* Example of use for OrderStatus class
 * Get the documents of a passed order
 */
require_once('../config/autoload.php');


$references = isset($_REQUEST['reference']) ? array($_REQUEST['reference']) : array();
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

// Prepare and execute the request
$lib = new OrderStatus();
/* 
    $reference is an array of Boxtal order references
    $type is the document type. Available values are:
        -"waybill" general waybill for the shipment
        -"delivery_waybill" waybill used by some carriers only (Colissimo for instance)
    $filename is the intended file title
*/
$lib->getOrderDocuments($references, 'waybill', 'my-waybill');