<?php
define('EMC_MODE', 'test'); // change to 'prod' to test production server (this WILL make an order if you test makeOrder function !)

if (EMC_MODE == 'prod') {
    define('EMC_USER', '');
    define('EMC_PASS', '');
    define('EMC_KEY', '');
} else {
    define('EMC_USER', 'codersEmc');
    define('EMC_PASS', 'codersEmc');
    define('EMC_KEY', 'codersEmc');
}



$uriExploded = explode("/", $_SERVER['REQUEST_URI']);
if (in_array("samples", $uriExploded) ||  in_array("test", $uriExploded)) {
    define("EMC_PARENT_DIR", "../");
} else {
    define("EMC_PARENT_DIR", "");
}


/**
 * function to handle API errors
 * @param  [type] $lib [description]
 * @return [type]      [description]
 */
function handle_errors($lib)
{
    if ($lib->resp_error) {
        echo "Invalid request: ";
        foreach ($lib->resp_errors_list as $m => $message) {
            echo "<br />".$message["message"];
        }
    } elseif ($lib->curl_error) {
        echo "Unable to send the request: ".$lib->curl_error_text;
    }
}
