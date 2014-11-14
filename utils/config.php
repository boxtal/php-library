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
    'user'	=> 'lionelT',
    'pass'	=> 'lionelT',
    'key'	=> 'lyzysyj1'
  ),
);

function handle_errors($lib)
{
	if ($lib->resp_error)
	{
    echo "Invalid request : ";
    foreach($lib->resp_errors_list as $m => $message)
    { 
      echo "<br />".$message["message"];
    }
	}
	elseif($lib->curl_error)
	{
		echo "Unable tu send the request : ".$lib->curl_error_text;
	}
}

?>