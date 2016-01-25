<?php
$credentials = array
(
  'test' => array
  (
    'user'	=> '',
    'pass'	=> '',
    'key'	=> ''
  ),
  'prod' => array
  (
    'user'	=> '',
    'pass'	=> '',
    'key'	=> ''
  ),
);

$env = 'test'; // change to 'prod' to test production server (this WILL make an order if you test makeOrder function !)

function handle_errors($lib)
{
	if ($lib->resp_error)
	{
    echo "Invalid request: ";
    foreach($lib->resp_errors_list as $m => $message)
    { 
      echo "<br />".$message["message"];
    }
	}
	elseif($lib->curl_error)
	{
		echo "Unable to send the request: ".$lib->curl_error_text;
	}
}

?>