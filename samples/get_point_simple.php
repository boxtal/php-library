<?php 
/*  Ce document a pour but d'exploiter des différentes méthodes de récupération des points de livraisons ou de retraits
 *  Chaque requête doit se faire une par une, et tous les résultat sont stoqués dans une même variable 
 */ 
ob_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once('../utils/header.php'); 
require_once('../utils/autoload.php');
$parcelPointsStyle = 'style="font-weight:bold;"';
// Exemple de traduction pour les codes d'erreur
$codesTranslated = array("http_file_not_found" => "Page n'existe pas", 
"type_not_correct" => "Please, select the right point type");

// Initialisation de la classe points relais
$pointCl = new Env_ParcelPoint(array("user" => $userData["login"], "pass" => $userData["password"], "key" => $userData["api_key"]));
// Example avec deux points relais, un pour RelaisColis, l'autre pour Sernam; pour ce faire
// on doit mettre $constructList en true
$pointCl->constructList = true;
// Récupération des points relais, un par un
// Chaque point relai est rajouté avec les autres dans $pointCl->points
$pointCl->getParcelPoint("dropoff_point", "SOGP-C3084");
$pointCl->getParcelPoint("dropoff_point", "SOGP-C3159"); 
$pointCl->getParcelPoint("dropoff_point", "SOGP-C3065"); 
$pointCl->getParcelPoint("dropoff_point", "SOGP-C3137");  
$pointCl->getParcelPoint("pickup_point", "SOGP-C3059"); 
$pointCl->getParcelPoint("pickup_point", "SOGP-C3210"); 
$pointCl->getParcelPoint("pickup_point", "SOGP-C3138"); 

// Vérificiation si la requête n'a pas provoqué d'erreur; sinon, on affiche les informations
if(!$pointCl->curlError && !$pointCl->respError) {
   // print_r($pointCl->points); 
?>
<p><b>Les points de retrait:</b></p>
<ul>
<?php foreach($pointCl->points["pickup_point"] as $point) { ?>
  <li><?php echo $point["name"];?> <br /><?php echo $point["description"];?></li>
<?php } ?>
</ul>
<br /><br />
<p><b>Les points de dépôt:</b></p>
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