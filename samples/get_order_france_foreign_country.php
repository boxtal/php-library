<?php
ob_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once $_SERVER['DOCUMENT_ROOT'].'/librairie/utils/autoload.php';

// création du destinataire et de l'expéditeur - classe séparée car dans l'avenir on voudra 
// peut-être gérer les carnets d'adresses ou gestion de compte à distance (via une smartphone par exemple)
$from = array("country" => "FR", "zipcode" => "75008", "type" => "particulier",
"ville" => "Paris", "adresse" => "1, rue du Test | logement 320", "civilite" => "Mme",
"prenom" => "Juliane", "nom" => "Julios", "email" => "bartosz@boxtale.com",
"tel" => "0601010101", "infos" => "Faire attention au chien");
$to = array("pays" => "AU", "code_postal" => "2000", "type" => "particulier",
"ville" => "Sydney", "adresse" => "1, rue de l'Olympiade | logement 1120", "civilite" => "Mme", 
"prenom" => "Julios", "nom" => "Juliosa", "email" => "bartosz@boxtale.com",
"tel" => "0601010101", "infos" => "Faire attention au chat");

// faire la cotation
$quotInfo = array("collecte_date" => "2011-04-29", "delay" => "aucun",  "content_code" => 10120,
"operator" => "UPSE",  
"reason" => "sale",

"valeur" => 1200,
"assurance.selected" => false,
"description" => "xzzzz",

"disponibilite.HDE" => "09:00", 
"disponibilite.HLE" => "19:00", 
"delivery_type" => "HOME");
$cotCl = new Env_Quotation(array("user" => "bbc", "pass" => "bbc", "key" => "bbc"));
$cotCl->setPerson("shipper", $from);
$cotCl->setPerson("recipient", $to);
$cotCl->setType("package", array("weight" => 2, "length" => 30, "width" => 44, "height" => 44));
$cotCl->setProforma(array(1 => array("description_en" => "my favorite cat",
"description_fr" => "mon chat préféré, vivant", "number" => 1, "value" => 1100),
2 => array("description_en" => "my favorite dog",
"description_fr" => "mon chien préféré", "number" => 2, "value" => 500)));
$orderPassed = $cotCl->makeOrder($quotInfo); 
if(!$cotCl->curlError && !$cotCl->respError) {
  if($orderPassed) {
    echo "L'envoi a été correctement réalisé sous référence ".$cotCl->command['ref'];
  }
  else {
    echo "L'envoi n'a pas été correctement réalisé. Une erreur s'est produite.";
  }
}
elseif($cotCl->respError) {
  echo "La requête n'est pas valide : ";
  foreach($cotCl->respErrorsList as $m => $message) { 
    echo "<br />".$message['message'];
  } die();
}
else {
  echo "<b>Une erreur pendant l'envoi de la requête </b> : ".$cotCl->curlErrorText;
  die();
}
?> 