<?php  
ob_start(); 
header('Content-Type: text/html; charset=utf-8'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once $_SERVER['DOCUMENT_ROOT'].'/librairie/utils/autoload.php';

$from = array("country" => "FR", "zipcode" => "44000",
"adresse" => "1, rue Racine", "type" => "particulier");
$to = array("country" => "FR", "zipcode" => "33000", 
"adresse" => "1, Rue des Faures", "type" => "particulier"); 
// faire la cotation
$quotInfo = array("collecte_date" => "2011-04-26", "delay" => "aucun",  "content_code" => 10120);
$cotCl = new Env_Quotation(array("user" => "bbc", "pass" => "bbc", "key" => "bbc"));
$cotCl->setPerson("shipper", $from);
$cotCl->setPerson("recipient", $to);
$cotCl->setType("package", array("weight" => 2, "length" => 30, "width" => 44, "height" => 44));
$cotCl->getQuotation($quotInfo);
if(!$cotCl->curlError) {
  $cotCl->getOffers(false); 
}
else {
  echo "<b>Une erreur pendant l'envoi de la requête </b> : ".$cotCl->curlErrorText;
  die();
}

if($_GET['format'] == "") {
 
?>
<style type="text/css">
table tr td {border:1px solid #000000; padding:5px; }
</style>
<table>
<thead><tr>
<td>Transp / Service</td><td>Prix</td><td>Collection</td><td>Livraison</td><td>Détails</td><td>Alertes</td>
<td>Informations</td>
</tr></thead>
<tbody>
<?php foreach($cotCl->offers as $o => $offre) { ?>
<tr>
<td><b><?php echo $o;?></b>. <?php echo $offre['operator']['label'];?> / <?php echo $offre['service']['code'];?></td>
<td><?php echo $offre['price']['tax-exclusive'];?> <?php echo $offre['price']['currency'];?></td>
<td><?php echo $offre['collection']['type'];?></td>
<td><?php echo $offre['delivery']['type'];?></td>
<td>
<?php echo implode('<br /> - ', $offre['characteristics']); ?>
</td>
<td>
<?php echo implode('<br /> - ', $offre['alerts']); ?>
</td>
<td>
<?php print_r($offre['mandatory']);?>
</td>
</tr>
<?php } ?>
</tbody>
</table>

<?php } elseif($_GET["format"] == "serialize") {
file_put_contents($_SERVER['DOCUMENT_ROOT'].'/test.txt', serialize($cotCl->offers));
  echo serialize($cotCl->offers);
  die();
} else { 
// cas requête JSON - utilisé dans la demo
  echo json_encode($cotCl->offers);
  die();
} 
?> 