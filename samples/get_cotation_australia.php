<?php
/*  Cet exemple illustre une simple cotation pour une expédition France - Australie
 */
ob_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once('../utils/header.php'); 
require_once('../utils/autoload.php');
$quotationPSStyle = 'style="font-weight:bold;"';

// Expéditeur et destinataire
$from = array("pays" => "FR", "code_postal" => "75002", "type" => "particulier",
"ville" => "Paris");
$to = array("pays" => "AU", "code_postal" => "2000", "type" => "particulier",
"ville" => "Sydney");

// Informations sur l'envoi
$quotInfo = array("collecte" => date("Y-m-d"), "delai" => "aucun",  "code_contenu" => 10120,
"colis.valeur" => 1200,
"colis.description" => "Des journaux");
$cotCl = new Env_Quotation(array("user" => $userData["login"], "pass" => $userData["password"], "key" => $userData["api_key"]));
$cotCl->setPerson("expediteur", $from);
$cotCl->setPerson("destinataire", $to);
$cotCl->setType("colis", array(
  1 => array("poids" => 4, "longueur" => 7, "largeur" => 8, "hauteur" => 11)
));
$orderPassed = $cotCl->getQuotation($quotInfo); 
// Si pas d'erreur CURL
if(!$cotCl->curlError) { print_r($pointCl->respErrorsList);
  // Si pas d'erreurs de la requête, on affiche le tableau
  if(!$cotCl->respError) {
    $cotCl->getOffers(false);
?>
<style type="text/css">
table tr td {border:1px solid #000000; padding:5px; }
</style>
<table>
<thead><tr>
<td>Transp / Service</td><td>Prix</td><td>Collection</td><td>Livraison</td><td>Détails</td><td>Alertes</td>
<td>Informations <br />à fournir</td>
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
<?php echo $offre['alert']; ?>
</td>
<td>
<?php foreach($offre['mandatory'] as $m => $mandatory) { ?>
- <?php echo $m; ?><br />
<?php } ?>
</td>
</tr>
<?php } ?>
</tbody>
</table>
<?php
  } 
  else {
    echo "La requête n'est pas valide : ";
    foreach($cotCl->respErrorsList as $m => $message) { 
      echo "<br />".$message["message"];
    }
  }
}
else {
  echo "<b>Une erreur pendant l'envoi de la requête </b> : ".$cotCl->curlErrorText;
  die();
}
require_once('../utils/footer.php');
?> 