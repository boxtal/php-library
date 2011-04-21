<?php
ob_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once $_SERVER['DOCUMENT_ROOT'].'/librairie/utils/autoload.php';

// c'est juste un exemple; l'utilisateur peut vouloir résoudre cela différement
$codesTranslated = array("http_file_not_found" => "Page n'existe pas");

// récupération des catégories de contenu principales
$pointCl = new Env_ParcelPoint(array("user" => "bbc", "pass" => "bbc", "key" => "bbc"));
$pointCl->constructList = true;
// $pointCl->getParcelPoint("dropoff_point", "SOGP-C3051"); 
$pointCl->getParcelPoint("pickup_point", "SERN-206078"); 

if(!$pointCl->curlError && !$pointCl->respError) {
  print_r($pointCl->points); 
}
elseif($pointCl->respError) {
  echo "La requête n'est pas valide : ";
  foreach($pointCl->respErrorsList as $m => $message) { 
    echo "<br />".$codesTranslated[$message["code"]]." - ".$message["url"];
  } die();
}
else {
  echo "<b>Une erreur pendant l'envoi de la requête </b> : ".$pointCl->curlErrorText;
  die();
}

?> 