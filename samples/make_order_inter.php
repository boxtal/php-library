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
$lib = new EnvQuotation($credentials[$env]);
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

// For an international send, you must specify the proforma
$lib->setProforma(
	array(
		1 => array(
			'description_en' => 'L\'Equipe newspaper from 1998',
			'description_fr' => 'le journal L\'Equipe du 1998',
			'nombre' => 1,
			'valeur' => 1000, 
			'origine' => 'FR',
			'poids' => 20
		)
	)
);
/* if you're sending more parcels
$lib->setProforma(
	array(
		1 => array(
			'description_en' => 'L\'Equipe newspaper from 1998',
			'description_fr' => 'le journal L\'Equipe du 1998',
			'nombre' => 1,
			'valeur' => 10, 
			'origine' => 'FR',
			'poids' => 1.2
		),
		2 => array(
			'description_en' => '300 editions of L\'Equipe newspaper from 1999',
			'description_fr' => '300 numéros de L\'Equipe du 1999',
			'nombre' => 300,
			'valeur' => 8, 
			'origine' => 'FR',
			'poids' => 0.1
		)
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