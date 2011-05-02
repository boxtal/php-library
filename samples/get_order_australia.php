<?php
/*  Cet exemple illustre une simple passation de commande France - Australie
 */
ob_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once('../utils/header.php');
require_once $_SERVER['DOCUMENT_ROOT'].'/librairie/utils/autoload.php';
$orderPSStyle = 'style="font-weight:bold;"';

// Expéditeur et destinataire
$from = array("pays" => "FR", "code_postal" => "75002", "type" => "particulier",
"ville" => "Paris", "adresse" => "41, rue Saint Augustin | floor nr 3", "civilite" => "M",
"prenom" => "Test_prenom", "nom" => "Test_nom", "email" => "dev@boxtale.com",
"tel" => "0601010101", "infos" => "");
$to = array("pays" => "AU", "code_postal" => "2000", "type" => "particulier",
"ville" => "Sydney", "adresse" => "King Street", "civilite" => "M", 
"prenom" => "Test_prenom_dst", "nom" => "Test_nom_dst", "email" => "dev@boxtale.com",
"tel" => "0601010101", "infos" => "Some informations about my shipment");

// Informations sur l'envoi
$quotInfo = array("collecte_date" => "2011-05-10", "delai" => "aucun",  "content_code" => 10120,
"operator" => "UPSE",
"reason" => "sale",
"valeur" => 1200,
"assurance.selected" => false,
"description" => "Des journaux",
"disponibilite.HDE" => "09:00", 
"disponibilite.HLE" => "19:00");
$cotCl = new Env_Quotation(array("user" => "login", "pass" => "pass", "key" => "api_cle"));
$cotCl->setPerson("expediteur", $from);
$cotCl->setPerson("destinataire", $to);
$cotCl->setType("colis", array("poids" => 11, "longueur" => 30, "largeur" => 44, "hauteur" => 44));
// Pour cet envoi on est obligé de joindre une facture proforma qui résume 2 objets expédiés
$cotCl->setProforma(array(1 => array("description_en" => "L'Equipe newspaper from 1998",
"description_fr" => "le journal L'Equipe du 1998",  "nombre" => 1, "valeur" => 10, 
"origine" => "FR", "poids" => 0.2),
2 => array("description_en" => "300 numbers of L'Equipe newspaper from 1999",
"description_fr" => "300 numéros de L'Equipe du 1999",  "nombre" => 300,  "valeur" => 8, 
"origine" => "FR", "poids" => 0.2)));
$orderPassed = $cotCl->makeOrder($quotInfo); 
if(!$cotCl->curlError && !$cotCl->respError) {
  if($orderPassed) {
    echo "L'envoi a été correctement réalisé sous référence ".$cotCl->order['ref'];
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
require_once('../utils/footer.php');
?> 