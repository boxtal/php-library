<?php
/* Example of use for EnvCarriersList class
 * Get all available carriers for a platform
 * Note that you need this request only if you plan to develop your own platform or module.
 */
$folder = '../';
require_once('../utils/header.php');
require_once('../utils/config.php');
require_once('../env/WebService.php');
require_once('../env/CarriersList.php');

$family = array(
	'1' => 'Economy',
	'2' => 'Express'
);
$zone = array(
	'1' => 'France',
	'2' => 'International',
	'3' => 'Europe'
);

/* Prepare and execute the request */
$env = 'test'; // use 'prod' for production
$locale = 'en-US'; // you can change this to 'fr-FR' or 'es-ES' for instance
$module_platform = 'prestashop';
$module_version = '3.0.0';
$lib = new EnvCarriersList($credentials[$env]);
$lib->setEnv($env);
$lib->setLocale($locale);
$lib->getCarriersList($module_platform,$module_version);

/* Show an array with carrier's informations */
if(!$lib->curl_error && !$lib->resp_error)
{ 
?>
<style type='text/css'>
	table tr td {border:1px solid #000000; padding:5px; }
</style>
<table>
	<tr>
		<td>Operator</td>
        <td>Service</td>
		<td>Operator code</td>
		<td>Service code</td>
		<td>Description</td>
		<td>Family</td>
		<td>Zone</td>
		<td>Parcel point drop-off</td>
		<td>Parcel point pick-up</td>
	</tr>
<?php	foreach($lib->carriers as $carrier){	?>
		<tr>
			<td><?php echo $carrier['ope_name']; ?></td>
            <td><?php echo $carrier['srv_name']; ?></td>
			<td><?php echo $carrier['ope_code']; ?></td>
			<td><?php echo $carrier['srv_code']; ?></td>
			<td><?php echo '<u>For sender</u> : '.$carrier['label_store'].'<br/><u>For recipient</u> : '.$carrier['description'].' ('.$carrier['description_store'].')'; ?></td>
			<td><?php echo $family[$carrier['family']]; ?></td>
			<td><?php echo $zone[$carrier['zone']]; ?></td>
			<td><?php echo $carrier['parcel_pickup_point']=='1'?'Yes':'No'; ?></td>
			<td><?php echo $carrier['parcel_dropoff_point']=='1'?'Yes':'No'; ?></td>
		</tr>
<?php	}	?>
</table>
<?php
}

handle_errors($lib);
require_once('../utils/footer.php');
?>
 
