<?php
$credentials = array
(
  'test' => array
  (
    'user'	=> 'codersEmc',
    'pass'	=> 'codersEmc',
    'key'	=> 'codersEmc'
  ),
  'prod' => array
  (
    'user'	=> 'arnauddutant',
    'pass'	=> 'developpeur',
    'key'	=> 'k1t40vnf'
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