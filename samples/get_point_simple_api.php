<?php /*  Ce document a pour but d'exploiter des différentes méthodes de récupération des points de livraisons ou de retraits
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

  
$typesTrad = array("exp" => "pickup_point", "dest" => "dropoff_point");

/* Dans le cas ou aucune variable get n'est specifiée => valeurs par defaut */
$points = isset($_GET['points'])?$_GET['points']:'SOGP-C3084,SOGP-C3159';
$qui = isset($_GET['qui'])?$_GET['qui']:'dest';

$pointsGet = explode(",", $points);
foreach($pointsGet as $p => $point) { 
  $pointCl->getParcelPoint($typesTrad[$qui], trim($point)); 
}
		 
foreach($pointCl->points[$typesTrad[$qui]] as $p => $point) { 
?> 
<p>
	<input name="pointrelais-<?php echo $qui;?>" type="radio" value="<?php echo $point["name"];?>|||<?php echo $point["address"];?>|||<?php echo $point["zipcode"];?>|||<?php echo $point["city"];?>" />
<?php echo $point["name"];?> <br /><?php echo $point["address"];?> <br />
<?php echo $point["zipcode"];?> <?php echo $point["city"];?> 
</p>
    <?php
  }
  
 
?> 