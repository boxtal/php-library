<?php  
/* Example of use for EnvCountry class  
 * Load all available countries or destinations from a country
 */ 
$folder = '../';
require_once('../utils/header.php');
require_once('../utils/config.php');
require_once('../env/WebService.php');
require_once('../env/Country.php');

// Prepare and execute the request
$env = 'test';
$locale = 'en-US'; // you can change this to 'fr-FR' or 'es-ES' for instance
$lib = new EnvCountry($credentials[$env]);
$lib->setEnv($env);
$lib->setLocale($locale);
$lib->getCountries();

if(!$lib->curl_error && !$lib->resp_error)
{
?>
<p>
	<label>Country list : </label>
	<select>
<?php foreach($lib->countries as $c => $country) { ?>  
			<option value="<?php echo $country['code'];?>"><?php echo $country['label'];?></option> 
<?php } ?>
	</select>
</p>
<?php
}
// Get a country from iso code (Netherlands)
$lib->getCountry("NL");

if(!$lib->curl_error && !$lib->resp_error)
{
?>

<p>Destinations to Netherlands : 
	<ul>
<?php foreach($lib->country as $c => $country) { ?>
		<li><?php echo $country["label"];?></li>
<?php } ?>
	</ul>
</p>
<?php
}
handle_errors($lib);
require_once('../utils/footer.php');
?>
