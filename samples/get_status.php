<?php
use \Emc\OrderStatus;

/* Example of use for OrderStatus class
 * Get the status of a passed order
 */
require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');


if (EMC_MODE == "prod") {
    $emcRef = "1605122984DHLEQA2DFR";
} else {
    $emcRef = "1606073393UPSE082ZFR";
}


// Prepare and execute the request
$lib = new OrderStatus();
$lib->getOrderInformations($emcRef);

echo "<h3>API OrderStatus :</h3>";
if (!$lib->curl_error && !$lib->resp_error) {
    // If you want to recieve order's status changes or documents, check for "url_push" param in samples/make_order.php
    echo '<pre>' . print_r($lib->order_info, true) . '</pre>';
} else {
    echo '<div class="alert alert-danger">';
    handle_errors($lib);
    echo'</div>';
}
?>
<div class="well well-sm">
    <button type="button" class="btn btn-xs btn-default" id="toogleDebug">
        Toggle Debug
    </button>
    <pre id="debug" style="display: none">
        <?php print_r(array_merge($lib->getApiParam(), array('API response :' => $lib->order_info))); ?>
    </pre>
</div>
<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');
