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

/* Préparation, envoi de la requête à l'API et reception de la réponse */
$lcCl = new Env_CarriersList(array("user" => $userData['login'], "pass" => $userData['password'], "key" => $userData['api_key']));
$lcCl->setEnv("test");

$lcCl->loadCarriersList("Prestashop","3.0.0");

$family = array(
	"1" => "Economique",
	"2" => "Expressiste"
);
$zone = array(
	"1" => "France",
	"2" => "International",
	"3" => "Europe"
);

echo "<pre>".print_r($lcCl->carriers,true)."</pre>";

/* If there is no errors, we display the datas */
if(!$lpCl->curlError && !$lpCl->respError) { 
?>
<style type="text/css">
	table tr td {border:1px solid #000000; padding:5px; }
</style>
<table>
	<tr>
		<td>Opérateur</td>
		<td>Service</td>
		<td>Description</td>
		<td>Famille</td>
		<td>Zone</td>
		<td>Depot point relais</td>
		<td>Retrait point relais</td>
	</tr>
<?php	foreach($lcCl->carriers as $carrier){	?>
		<tr>
			<td><?php echo $carrier['ope_name']." (".$carrier['ope_code'].")"; ?></td>
			<td><?php echo $carrier['srv_name']." (".$carrier['srv_code'].")"; ?></td>
			<td><?php echo "<u>Label</u> : ".$carrier['label_store']."<br/><u>Description</u> : ".$carrier['description']." (".$carrier['description_store'].")"; ?></td>
			<td><?php echo $family[$carrier['family']]; ?></td>
			<td><?php echo $zone[$carrier['zone']]; ?></td>
			<td><?php echo $carrier['parcel_pickup_point']=="1"?"Oui":"Non"; ?></td>
			<td><?php echo $carrier['parcel_dropoff_point']=="1"?"Oui":"Non"; ?></td>
		</tr>
<?php	}	?>
</table>
<?php
}
/* Cas d'erreur */
elseif($lcCl->respError) {
  echo "La requête n'est pas valide : ";
  foreach($lcCl->respErrorsList as $m => $message) { 
    echo "<br />".$message['message'];
  }
}
else {
	"<b>Une erreur pendant l'envoi de la requête </b> : ".$cotCl->curlErrorText; 
}
require_once('../utils/footer.php');?>
 
