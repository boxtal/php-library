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
$lpCl = new Env_ListPoints(array("user" => $userData["login"], "pass" => $userData["password"], "key" => $userData["api_key"]));
$lpCl->setEnv('test');
$params = array('srv_code' => 'RelaisColis', "collecte"=> "exp", 'pays' => 'FR', 'cp' => '75011', 'ville' => 'PARIS');
$lpCl->getListPoints("SOGP", $params);

/* If there is no errors, we display the datas */
if(!$lpCl->curlError && !$lpCl->respError) { 
?>
<style type="text/css">
	table tr td {border:1px solid #000000; padding:5px; }
</style>
<?php
$jourSemaine = array(
	1 => 'Lundi',
	2 => 'Mardi',
	3 => 'Mercredi',
	4 => 'Jeudi',
	5 => 'Vendredi',
	6 => 'Samedi',
	7 => 'Dimanche'
);
?>
<table>
	<tr>
		<td>Code</td>
		<td>Nom</td>
		<td>Adresse</td>
		<td>Ville</td>
		<td>CP</td>
		<td>Pays</td>
		<td>Téléphone</td>
		<td>Déscription</td>
		<td>Calendrier</td>
	</tr>
<?php	foreach($lpCl->listPoints as $point){	?>
		<tr>
			<td><?php echo $point['code']; ?></td>
			<td><?php echo $point['name']; ?></td>
			<td><?php echo $point['address']; ?></td>
			<td><?php echo $point['city']; ?></td>
			<td><?php echo $point['zipcode']; ?></td>
			<td><?php echo $point['country']; ?></td>
			<td><?php echo $point['phone']; ?></td>
			<td><?php echo $point['description']; ?></td>
			<td>
				<table>
					<tr>
						<td>Jour semaine</td>
						<td>Ouverture am</td>
						<td>Fermeture am</td>
						<td>Ouverture pm</td>
						<td>Fermeture pm</td>
					</tr>
<?php			foreach($point['days'] as $day){	?>
						<tr>
							<td><?php echo $jourSemaine[$day['weekday']]; ?></td>
							<td><?php echo $day['open_am']; ?></td>
							<td><?php echo $day['close_am']; ?></td>
							<td><?php echo $day['open_pm']; ?></td>
							<td><?php echo $day['close_pm']; ?></td>
						</tr>
<?php			}	?>
				</table>
			</td>
		</tr>
<?php	}	?>
</table>
<?php
}
/* Cas d'erreur */
elseif($lpCl->respError) {
  echo "La requête n'est pas valide : ";
  foreach($lpCl->respErrorsList as $m => $message) { 
    echo "<br />".$message['message'];
  }
}
else {
	"<b>Une erreur pendant l'envoi de la requête </b> : ".$cotCl->curlErrorText; 
}
require_once('../utils/footer.php');?>
 
