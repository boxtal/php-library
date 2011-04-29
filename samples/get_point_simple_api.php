<?php 
ob_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once $_SERVER['DOCUMENT_ROOT'].'/librairie/utils/autoload.php';
  
$typesTrad = array("exp" => "pickup_point", "dest" => "dropoff_point");

// c'est juste un exemple; l'utilisateur peut vouloir résoudre cela différement
$codesTranslated = array("http_file_not_found" => "Page n'existe pas");
// récupération des catégories de contenu principales
$pointCl = new Env_ParcelPoint(array("user" => "bbc", "pass" => "bbc", "key" => "bbc"));
$pointCl->constructList = true;

$pointsGet = explode(",", $_GET["points"]);
foreach($pointsGet as $p => $point) { 
  $pointCl->getParcelPoint($typesTrad[$_GET["qui"]], trim($point)); 
} 
  
     
foreach($pointCl->points[$typesTrad[$_GET["qui"]]] as $p => $point) { 
    ?> 
<p><input name="pointrelais-<?php echo $_GET["qui"];?>" type="radio" value="<?php echo $point["name"];?>|||<?php echo $point["address"];?>|||<?php echo $point["zipcode"];?>|||<?php echo $point["city"];?>" />
<?php echo $point["name"];?> <br /><?php echo $point["address"];?> <br />
<?php echo $point["zipcode"];?> <?php echo $point["city"];?> 
</p>
    <?php
  }
  
 
?> 