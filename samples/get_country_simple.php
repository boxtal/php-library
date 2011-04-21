<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once $_SERVER['DOCUMENT_ROOT'].'/librairie/utils/autoload.php';

// récupération des catégories de contenu principales
$countryCl = new Env_Country(array("user" => "bbc", "pass" => "bbc", "key" => "bbc"));
$countryCl->getCountries();

// récupération d'un pays
$countryCl->getCountry("NL");
print_r($countryCl->country);

// récupération d'un autre pays
$countryCl->getCountry("FR");
print_r($countryCl->country);
?>

<select name="categories">
<?php foreach($countryCl->countries as $c => $country) { ?>  
<option value="<?php echo $country['code'];?>"><?php echo $country['label'];?></option> 
<?php } ?>
</select>