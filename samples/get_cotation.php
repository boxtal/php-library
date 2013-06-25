<?php  
/*  Ce document a pour but de récupérer des offres de transport pour un devis Nantes - Bordeaux (1 colis d'un poids de 2 kg, 
 *  dont la catégorie de contenu est Journaux). On a besoin d'adresses exactes afin de pouvoir détermines les points relais pour 
 *  le service RelaisColis.
 */
require_once('../utils/header.php');
ob_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE); 
require_once('../utils/autoload.php');
$quotationStyle = 'style="font-weight:bold;"';

// Précision de l'expéditeur et du destinataire
$to = array("pays" => "FR", "code_postal" => "75002", "ville" => "Paris", "type" => "particulier", "adresse" => "41, rue Saint Augustin");
$from = array("pays" => "FR", "code_postal" => "13002",   "ville" => "Marseille", "type" => "particulier", "adresse" => "1, rue Chape"); 
// Informations sur la cotation (date d'enlèvement, le délai, le code de contenu)
$quotInfo = array("collecte" => date("Y-m-d"), "delai" => "aucun",  
"code_contenu" => 10120);
// Initialisation de la classe
$cotCl = new Env_Quotation(array("user" => $userData["login"], "pass" => $userData["password"], "key" => $userData["api_key"]));
// Initialisation de l'expéditeur et du destinataire
$cotCl->setPerson("expediteur", $from);
$cotCl->setPerson("destinataire", $to);
// Précision de l'environnement de travail 
$cotCl->setEnv('test'); 
// Initialisation du type d'envoi
$cotCl->setType("colis", array(
1 => array("poids" => 3, "longueur" => 30, "largeur" => 20, "hauteur" => 20)
// , 2 => array("poids" => 21, "longueur" => 7, "largeur" => 8, "hauteur" => 11)
)
);
$cotCl->getQuotation($quotInfo);
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
<td><?php echo $offre['collection']['type'];
	if($offre['collection']['type'] === "DROPOFF_POINT" || $offre['collection']['type'] === "POST_OFFICE") {
		echo "<br/><br/>Liste pr :";
		foreach($offre['mandatory']['depot.pointrelais']['array'] as $pp) {
			echo "<br/>". $pp;
		}
	}
?></td>
<td>
<?php 
	echo $offre['delivery']['type'];
	if($offre['delivery']['type'] === "PICKUP_POINT") {
		echo "<br/><br/>Liste pr :";
		foreach($offre['mandatory']['retrait.pointrelais']['array'] as $pp) {
			echo "<br/>". $pp;
		}
	}
?>
</td>
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
