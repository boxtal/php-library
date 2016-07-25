<?php
use \Emc\News;

/* Example of use for News class
 * Load all available News
 */

require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');


/* Prepare and execute the request */
$lib = new News();

$module_version = '3.0.0';
$module_platform = 'prestashop';

$lib->loadNews($module_platform, $module_version);
echo "<h3>API News :</h3>";
if (!$lib->curl_error && !$lib->resp_error) {
    // If you want to recieve order's status changes or documents, check for "url_push" param in samples/make_order.php
    echo '<pre>' . print_r($lib->news, true) . '</pre>';
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
        <?php print_r(array_merge($lib->getApiParam(), array('API response :' => $lib->news))); ?>
    </pre>
</div>
<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');