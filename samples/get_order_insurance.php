<?php
/*  Cet exemple vous permet de passer une commande avec l'assurance.
 */ 
ob_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once('../utils/header.php');
require_once $_SERVER['DOCUMENT_ROOT'].'/librairie/utils/autoload.php';
$orderPMAStyle = 'style="font-weight:bold;"';
// Informations sur l'expéditeur et le destinataire 
$from = array("pays" => "FR", "code_postal" => "75002", "type" => "particulier",
"ville" => "Paris", "adresse" => "41, rue Saint-Augustin | 3e étage", 
"civilite" => "M", "prenom" => "Développeur", "nom" => "Boxtale", "email" => "dev@boxtale.com",
"tel" => "0601010101", "infos" => "Frapper 3 fois");
$to = array("pays" => "FR", "code_postal" => "13005", "type" => "particulier",
"ville" => "Marseille", "adresse" => "1, rue Saint-Thome",
"civilite" => "Mme", "prenom" => "Autre prénom", "nom" => "Nom du destinataire", 
"email" => "dev@boxtale.com", "tel" => "0601010101", "infos" => "");

// Informations sur la cotation
$quotInfo = array("collecte" => "2011-05-10", 
"delai" => "aucun",  "code_contenu" => 10120,
"operateur" => "UPSE", // "service" => "Standard" commenté, vous pouvez décommenter pour passer une commande en service Express 
"disponibilite.HDE" => "09:00", 
"disponibilite.HLE" => "19:00",
"assurance.selection" => true,
"valeur" => 120,
"prix_max_ttc" => 40,
"description" => "Le Monde, années 1990-1992"
);
$cotCl = new Env_Quotation(array("user" => "login", "pass" => "pass", "key" => "api_cle"));
$cotCl->setPerson("expediteur", $from);
$cotCl->setPerson("destinataire", $to);
$cotCl->setType("colis", array("poids" => 2, "longueur" => 30, "largeur" => 44, "hauteur" => 44));
$orderPassed = $cotCl->makeOrder($quotInfo, true);
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
  }
}
else {
  echo "<b>Une erreur pendant l'envoi de la requête </b> : ".$cotCl->curlErrorText; 
}
require_once('../utils/footer.php');?>
 