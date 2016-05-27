<?php
define("EMC_MODE", "test"); // change to 'prod' to test production server (this WILL make an order if you test makeOrder function !)

if (EMC_MODE == "prod") {
    define("EMC_USER", "abahet");
    define("EMC_PASS", "boxtale");
    define("EMC_KEY", "h2ucikm0");
} else {
    define("EMC_USER", "abahet");
    define("EMC_PASS", "boxtale");
    define("EMC_KEY", "h2ucikm0");
    //define("EMC_KEY", "0mqfew54");
}

$uriExploded = explode("/", $_SERVER['REQUEST_URI']);

if (in_array("samples", $uriExploded) ||  in_array("test", $uriExploded)) {
    define("EMC_PARENT_DIR", "../");
} else {
   define("EMC_PARENT_DIR", "");
}


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


$week_days = array(
    1 => 'Monday',
    2 => 'Tuesday',
    3 => 'Wednesday',
    4 => 'Thursday',
    5 => 'Friday',
    6 => 'Saturday',
    7 => 'Sunday'
);
