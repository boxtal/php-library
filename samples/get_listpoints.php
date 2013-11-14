<?php
/*  Cet exemple vous permet de passer une commande. L'envoi est composé d'informations basiques (expéditeur, destinataire, type) 
 *  et ne contient pas d'options supplémentaires. Il possède uniquement un filtre selon lequel le montant de la commande ne peut 
 *  pas dépasser 50€ ttc.
 */ 
ob_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once('../utils/header.php');
require_once('../utils/autoload.php');
$orderPMStyle = 'style="font-weight:bold;"';

$lpCl = new Env_ListPoints(array("user" => $userData["login"], "pass" => $userData["password"], "key" => $userData["api_key"]));
$params = array('srv_code' => 'RelaisColis', 'pays' => 'FR', 'cp' => '75011', 'ville' => 'PARIS');
$lpCl->getListPoints("SOGP", $params);
if(!$lpCl->curlError && !$lpCl->respError) { 
	$lpCl->dumpListPoints();
}
elseif($lpCl->respError) {
  echo "La requête n'est pas valide : ";
  foreach($lpCl->respErrorsList as $m => $message) { 
    echo "<br />".$message['message'];
  }
}
else {
  echo "<b>Une erreur pendant l'envoi de la requête </b> : ".$cotCl->curlErrorText; 
}
require_once('../utils/footer.php');?>
 
