<?php
use \Emc\OrderStatus;

/* Example of use for EnvOrderStatus class  
 * Get the status of a passed order
 */
require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');

// Prepare and execute the request
$lib = new OrderStatus("1605251836MONR01DQFR");

if(!$lib->curl_error && !$lib->resp_error)
{
	// If you want to recieve order's status changes or documents, check for "url_push" param in samples/make_order.php
	echo '<pre>'.print_r($lib->order_info,true).'</pre>';
} else {
    echo '<div class="alert alert-danger">';
    handle_errors($lib);
    echo'</div>';
}

require_once(EMC_PARENT_DIR.'layout/footer.php');