<?php
/* Example of use for EnvListPoints class  
 * Make an order
 */

require_once('../utils/config.php');
require_once('../env/WebService.php');
require_once('../env/Quotation.php');

// shipper and recipient's address
$from = array(
	'pays' => 'FR', 
	'code_postal' => '13002',   
	'ville' => 'Marseille', 
	'type' => 'particulier', 
	'adresse' => '1, rue Chape',
	'civilite' => 'M',
	'prenom' => 'prenom',
	'nom' => 'nom',
	'email' => 'informationapi@envoimoinscher.com',
	'tel' => '0606060606',
	'infos' => 'Some informations about my shipment'
); 
$to = array(
	'pays' => 'FR', 
	'code_postal' => '75002', 
	'ville' => 'Paris', 
	'type' => 'particulier', 
	'adresse' => '41, rue Saint Augustin',
	'civilite' => 'M',
	'prenom' => 'prenom',
	'nom' => 'nom',
	'email' => 'informationapi@envoimoinscher.com',
	'tel' => '0606060606',
	'infos' => 'Some informations about my shipment'
);
	
/*
 * $quot_params contains all additional parameters for your request, it includes filters or offer's options 
 * A list of all possible parameters is available here : http://ecommerce.envoimoinscher.com/api/documentation/commandes/
 * For an order, you have to provide at least all offer's mandatory parameters returned by the quotation
 * You can also find all optional parameters (filter not included) in the same quotation
 */
$quot_params = array(
	'collecte' => date('Y-m-d'),
	'delay' => 'aucun',
	'content_code' => 10120,
	'colis.description' => "books",
	'assurance.selected' => false,
	'depot.pointrelais' => 'CHRP-POST',
	// you can find more informations about what is sent on this url here : http://ecommerce.envoimoinscher.com/api/documentation/url-de-push
	'url_push' => 'www.my-website.com/push.php&order=N',
	// even if these parameters are optional we highly recommend you to set the operator and service you want
	'operator' => 'CHRP',
	'service' => 'Chrono18',
);

// Prepare and execute the request
$env = 'test';
$lib = new EnvQuotation($credentials[$env]);
$lib->setPerson('shipper', $from);
$lib->setPerson('recipient', $to);
$lib->setEnv($env);
$lib->setType('colis',
	array(
		1 => array('poids' => 21, 'longueur' => 7, 'largeur' => 8, 'hauteur' => 11), 
		2 => array('poids' => 21, 'longueur' => 7, 'largeur' => 8, 'hauteur' => 11)
  )
);
$orderPassed = $lib->makeOrder($quot_params); 

if(!$lib->curl_error && !$lib->resp_error)
{
  if($orderPassed) {
    echo 'Order passed with the reference '.$lib->order['ref'];
  }
  else {
    echo 'Your order has been refused';
  }
}

handle_errors($lib);
?> 