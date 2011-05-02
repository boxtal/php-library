<?php 
/*  Ce document a pour but d'exploiter des différentes méthodes de récupération des catégories et des contenus (sous-catégories). 
 *  Vous pouvez ainsi télécharger uniquement les catégories, lister les sous-catégories pour une seule ou pour toutes les catégories. 
 */ 
ob_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once('../utils/header.php');
require_once $_SERVER['DOCUMENT_ROOT'].'/librairie/utils/autoload.php';
$parcelPointsStyle = 'style="font-weight:bold;"';
// Exemple de traduction pour les codes d'erreur
$codesTranslated = array("http_file_not_found" => "Page n'existe pas", 
"type_not_correct" => "Please, select the right point type");

// Initialisation de la classe points relais
$pointCl = new Env_ParcelPoint(array("user" => "bbc", "pass" => "bbc", "key" => "bbc"));
// Example avec deux points relais, un pour RelaisColis, l'autre pour Sernam; pour ce faire
// on doit mettre $constructList en true
$pointCl->constructList = true;
// Récupération des points relais, un par un
$pointCl->getParcelPoint("dropoff_point", "SOGP-C3051");
$pointCl->getParcelPoint("dropoff_point", "SOGP-Z9014"); 
$pointCl->getParcelPoint("dropoff_point", "SOGP-A1153"); 
$pointCl->getParcelPoint("dropoff_point", "SOGP-O1164");  
$pointCl->getParcelPoint("pickup_point", "SERN-206078"); 
$pointCl->getParcelPoint("pickup_point", "SERN-206069"); 
$pointCl->getParcelPoint("pickup_point", "SERN-206044"); 

// Vérificiation si la requête n'a pas provoqué d'erreur; sinon, on affiche les informations
if(!$pointCl->curlError && !$pointCl->respError) {
   // print_r($pointCl->points); 
?>
<p><b>Les points de dépôt:</b></p>
<ul>
<?php foreach($pointCl->points["pickup_point"] as $point) { ?>
  <li><?php echo $point["name"];?> <br /><?php echo $point["description"];?></li>
<?php } ?>
</ul>
<br /><br />
<p><b>Les points de retrait:</b></p>
<ul>
<?php foreach($pointCl->points["dropoff_point"] as $point) { ?>
  <li><?php echo $point["name"];?> <br /><?php echo $point["address"];?>, <?php echo $point["zipcode"];?> <?php echo $point["city"];?></li>
<?php } ?>
</ul>
<?php
}
// Une erreur de résponse (un paramètre incorrect, un élément manquant) est apparue
elseif($pointCl->respError) {
  echo "La requête n'est pas valide : ";
  foreach($pointCl->respErrorsList as $m => $message) { 
    echo "<br />".$codesTranslated[$message["code"]]." - ".$message["url"];
  }
}
// Une erreur d'exécution CURL
else {
  echo "<b>Une erreur pendant l'envoi de la requête </b> : ".$pointCl->curlErrorText;
}
require_once('../utils/footer.php');?>  