<?php
use \Emc\Parameters;

/* Example of use for Parameters class
 * Load all available Parameters
 */

require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');


/* Prepare and execute the request */
$lib = new Parameters();

$lib->getParameters();
echo "<h3>API News :</h3>";
if (!$lib->curl_error && !$lib->resp_error) {
    // If you want to recieve order's status changes or documents, check for "url_push" param in samples/make_order.php
    echo '<pre>' . print_r($lib->parameters, true) . '</pre>';
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
        <?php print_r(array_merge($lib->getApiParam(), array('API response :' => $lib->parameters))); ?>
    </pre>
</div>
<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');