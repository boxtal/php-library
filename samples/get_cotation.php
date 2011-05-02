<?php  
/*  Ce document a pour but de récupérer des offres de transport pour un devis Nantes - Bordeaux (1 colis d'un poids de 2 kg, 
 *  dont la catégorie de contenu est Journaux). On a besoin d'adresses exactes afin de pouvoir détermines les points relais pour 
 *  le service RelaisColis.
 */
require_once('../utils/header.php');
ob_start();
header('Content-Type: text/html; charset=utf-8'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once $_SERVER['DOCUMENT_ROOT'].'/librairie/utils/autoload.php';
$quotationStyle = 'style="font-weight:bold;"';

// Précision de l'expéditeur et du destinataire
$from = array("pays" => "FR", "code_postal" => "44000", "type" => "particulier", "adresse" => "1, rue Racine");
$to = array("pays" => "FR", "code_postal" => "33000",   "type" => "particulier", "adresse" => "1, rue du Grand Lebrun"); 
// Informations sur la cotation (date d'enlèvement, le délai, le code de contenu)
$quotInfo = array("collecte_date" => "2011-05-11", "delai" => "aucun",  
"content_code" => 10120);
// Initialisation de la classe
$cotCl = new Env_Quotation(array("user" => "login", "pass" => "pass", "key" => "api_cle"));
// Initialisation de l'expéditeur et du destinataire
$cotCl->setPerson("expediteur", $from);
$cotCl->setPerson("destinataire", $to);
// Initialisation du type d'envoi
$cotCl->setType("colis", array("poids" => 2, "longueur" => 30, "largeur" => 44, "hauteur" => 44));
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
<td><?php echo $offre['collection']['type'];?></td>
<td><?php echo $offre['delivery']['type'];?></td>
<td>
<?php echo implode('<br /> - ', $offre['characteristics']); ?>
</td>
<td>
<?php echo implode('<br /> - ', $offre['alerts']); ?>
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