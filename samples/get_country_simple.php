<?php  
/*  Ce document a pour but d'exploiter des différentes méthodes de récupération des pays.
 * 
 */ 
ob_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once('../utils/header.php'); 
require_once('../utils/autoload.php');
$countriesStyle = 'style="font-weight:bold;"';

// Initialisation de la classe pays
$countryCl = new Env_Country(array("user" => $userData["login"], "pass" => $userData["password"], "key" => $userData["api_key"]));
// Récupération des pays
$countryCl->getCountries();
?>
<p>
	<label for="countries">Sélectionnez votre pays : </label>
	<select id="countries" name="countries">
<?php foreach($countryCl->countries as $c => $country) { ?>  
			<option value="<?php echo $country['code'];?>"><?php echo $country['label'];?></option> 
<?php } ?>
	</select>
</p>
<?php
// Récupération d'un pays (Pays-Bas)
$countryCl->getCountry("NL");
?>
<p>Les destinations vers les Pays-Bas : 
	<ul>
<?php foreach($countryCl->country as $c => $country) { ?>
		<li><?php echo $country["label"];?></li>
<?php } ?>
	</ul>
</p>
<?php require_once('../utils/footer.php'); ?>
