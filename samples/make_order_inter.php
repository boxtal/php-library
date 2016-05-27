<?php
/* Example of use for EnvListPoints class  
 * Make an international order, the difference with a "normal" make order is in the proforma
 */
$folder = '../';
require_once('../utils/header.php');
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
	'infos' => 'Some informations about this address'
); 
$to = array(
	'pays' => 'AU', 
	'code_postal' => '2000', 
	'ville' => 'Sydney', 
	'type' => 'particulier', 
	'adresse' => 'King Street',
	'civilite' => 'M',
	'prenom' => 'prenom',
	'nom' => 'nom',
	'email' => 'informationapi@envoimoinscher.com',
	'tel' => '0606060606',
	'infos' => 'Some informations about this address'
);
	
/*
 * $quot_params contains all additional parameters for your request, it includes filters or offer's options 
 * A list of all possible parameters is available here : http://ecommerce.envoimoinscher.com/api/documentation/commandes/
 * For an order, you have to provide at least all offer's mandatory parameters returned by the quotation
 * You can also find all optional parameters (non filter) in the same quotation
 */
$quot_params = array(
	'collecte' => date('Y-m-d'),
	'delay' => 'aucun',
	'content_code' => 10120,
	'raison' => 'sale', // for a list of authorized values see $ship_reasons (right-hand side values) in Quotation.php
	'colis.valeur' => 1200,
	'assurance.selected' => false,
	'colis.description' => 'Des journaux',
	'disponibilite.HDE' => '09:00', 
	'disponibilite.HLE' => '19:00',
	// you can find more informations about what is sent on this url here : http://ecommerce.envoimoinscher.com/api/documentation/url-de-push
	'url_push' => 'www.my-website.com/push.php&order=N',
	// even if these parameters are optional we highly recommend you to set the operator and service you want
	'operator' => 'UPSE',
	'service' => 'ExpressSaver'
);

// Prepare and execute the request
$env = 'test';
$locale = 'en-US'; // you can change this to 'fr-FR' or 'es-ES' for instance
$lib = new Quotation($credentials[$env]);
$lib->setPerson('shipper', $from);
$lib->setPerson('recipient', $to);
$lib->setEnv($env);
$lib->setLocale($locale);
$lib->setType('colis',
	array(
		1 => array('poids' => 21, 'longueur' => 7, 'largeur' => 8, 'hauteur' => 11)
  )
);

/* Optionally you can send two parcels in one order like this
$lib->setType('colis',
	array(
		1 => array('poids' => 21, 'longueur' => 7, 'largeur' => 8, 'hauteur' => 11), 
		2 => array('poids' => 15, 'longueur' => 9, 'largeur' => 8, 'hauteur' => 11)
  )
);
*/



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
require_once('../utils/footer.php');
?> 