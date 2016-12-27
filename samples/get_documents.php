<?php
use \Emc\OrderStatus;

/* Example of use for OrderStatus class
 * Get the documents of a passed order
 */
require_once('../config/autoload.php');


$reference = isset($_REQUEST['reference']) ? $_REQUEST['reference'] : '';
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

// Prepare and execute the request
$lib = new OrderStatus();
/* 
    $reference is the order Boxtal reference
    $type is the document type. Available values are:
        -"waybill" general waybill for the shipment
        -"delivery_waybill" waybill used by some carriers only (Colissimo for instance)
*/
$lib->getOrderDocuments($reference, 'waybill');