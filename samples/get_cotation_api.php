<?php  
/*  Ce document a pour but de récupérer des offres de transport pour un devis Paris - Marseille (1 colis d'un poids de 2 kg, 
 *  dont la catégorie de contenu est Journaux). On a besoin d'adresses exactes afin de pouvoir détermines les points relais pour 
 *  le service RelaisColis.
 */
ob_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once('../utils/header.php'); 
require_once('../utils/autoload.php');
$quotationPSStyle = 'style="font-weight:bold;"';

foreach($_GET as $k => $get) {
  $_GET[$k] = mb_convert_encoding(urldecode($_GET[$k]), "UTF-8");
}

// création du destinataire et de l'expéditeur - classe séparée car dans l'avenir on voudra 
// peut-être gérer les carnets d'adresses ou gestion de compte à distance (via une smartphone par exemple)
$to = array(
	"pays" => "FR", 
	"code_postal" => "75002", 
	"ville" => "Paris", 
	"type" => "particulier", 
	"adresse" => "41, rue Saint Augustin"
	);
$from = array(
	"pays" => "FR", 
	"code_postal" => "13002",   
	"ville" => "Marseille", 
	"type" => "particulier", 
	"adresse" => "1, rue Chape"); 
	
// On créé la cotation
$date = new DateTime();
$date->add(new DateInterval('P10D'));
$quotInfo = array("collecte" => $date->format('Y-m-d'), "delay" => "aucun",  "content_code" => 50113);
if($_GET["ope"] != "" && $_GET["ope"] != "all") { 
  $quotInfo["operator"] = $_GET["ope"];
}
$cotCl = new Env_Quotation(array("user" => $userData["login"], "pass" => $userData["password"], "key" => $userData["api_key"]));
// Initialisation de l'expéditeur et du destinataire
$cotCl->setPerson("shipper", $from);
$cotCl->setPerson("recipient", $to);
// Précision de l'environnement de travail 
$cotCl->setEnv('test'); 
// Initialisation du type d'envoi
$cotCl->setType(
	"colis", 
	array(
		1 => array(
			"poids" => 1, 
			"longueur" => 18, 
			"largeur" => 18,
			"hauteur" => 18
		)
	)
	);
	
$cotCl->getQuotation($quotInfo);
if($cotCl->curlError) {     
  echo "<b>Une erreur pendant l'envoi de la requête </b> : ".$cotCl->curlErrorText;   
  die();     
}    
elseif($cotCl->respError) {   
  echo "La requête n'est pas valide : ";   
  foreach($cotCl->respErrorsList as $m => $message) { 
    echo "<br /><b>".$message['message']."</b>";    
  }  
  die();  
}
else {
	?>
<style type="text/css">
table tr td {border:1px solid #000000; padding:5px; }
</style>
<table>
	<?php
  $cotCl->getOffers(true);
  foreach($cotCl->offers as $o => $offre) {
?>
	<tr id="ope-<?php echo $o;?>-tr">
		<td><input type="radio" name="ope" id="ope-<?php echo $o;?>" value="<?php echo $offre["operator"]["code"];?>" class="chkbox selectOpe" /> <label for="ope-<?php echo $o;?>">choisir cette offre</label></td>
		<td><img src="<?php echo $offre["operator"]["logo"];?>" alt="" /></td>
		<td>
<?php 
			foreach($offre["characteristics"] as $c => $char) {
				echo $char.'<br />';  
				unset($offre["characteristics"][$c]);  
				if($c == 3) { 
					break; 
				} 
			}  
?>
			<span id="char-<?php echo $o;?>" class="hidden"><?php echo implode("<br /> - ", $offre["characteristics"]); ?></span>
			<p>Mandatory : </p>
			<ul><?php foreach($offre['mandatory'] as $m => $mandatory) { ?><li><?php echo $m; ?></li><?php } ?></ul>
		</td>
		<td class="price">
<?php echo $offre['price']['tax-exclusive'];?>€
			<input type="hidden" name="ope-<?php echo $o;?>-price" id="ope-<?php echo $o;?>-price" value="<?php echo htmlspecialchars($offre['price']['tax-exclusive']);?>" />
			<input type="hidden" name="ope-<?php echo $o;?>-operator" id="ope-<?php echo $o;?>-operator" value="<?php echo htmlspecialchars($offre['operator']['label']);?>" />
			<input type="hidden" name="ope-<?php echo $o;?>-service" id="ope-<?php echo $o;?>-service" value="<?php echo htmlspecialchars($offre['service']['label']);?>" />
			<input type="hidden" name="ope-<?php echo $o;?>-infos" id="ope-<?php echo $o;?>-infos" value="<?php echo htmlspecialchars(implode("<br/> - ", $offre["characteristics"]));?>" />
		</td>
		<td>
<?php   	
				if(count($offre['mandatory']['depot.pointrelais']) > 0) {
?>
					<p class="arrow smaller selectPr">Points de proximité de départ</p>
					<ul>
						<?php foreach($offre['mandatory']['depot.pointrelais']['array'] as $p) echo '<li>'.$p.'</li>'; ?>
					</ul>
<?php 	} ?>
				<br />
<?php 	
				if(count($offre['mandatory']['retrait.pointrelais']) > 0) {
					$pr = explode(" ", $offre['mandatory']['retrait.pointrelais']['type']);
					foreach($pr as $p => $point) {
						if(trim($point) != "") {
							$poi[$p] = trim($point);
						}
					}
?>
					<p class="arrow smaller selectPr">Points de proximité d'arrivée</p>
					<ul>
						<?php foreach($offre['mandatory']['depot.pointrelais']['array'] as $p) echo '<li>'.$p.'</li>'; ?>
					</ul>
<?php 	} ?>
		</td>
</tr>
<?php
}
}
?> 
</table>