<?php
/* Example of use for EnvCarriersList class
 * Get all available carriers for a platform
 * Note that you need this request only if you plan to develop your own platform or module.
 */
 
require_once('../utils/config.php');
require_once('../env/WebService.php');
require_once('../env/CarriersList.php');

$family = array(
	'1' => 'Economic',
	'2' => 'Express'
);
$zone = array(
	'1' => 'France',
	'2' => 'International',
	'3' => 'Europe'
);

/* Prepare and execute the request */
$env = 'test';
$module_platform = 'prestashop';
$module_version = '3.0.0';
$lib = new EnvCarriersList($credentials[$env]);
$lib->setEnv($env);
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
		<td>Description</td>
		<td>Family</td>
		<td>Zone</td>
		<td>Dropoff on parcel point</td>
		<td>Pickup on parcel point</td>
	</tr>
<?php	foreach($lib->carriers as $carrier){	?>
		<tr>
			<td><?php echo $carrier['ope_name'].' ('.$carrier['ope_code'].')'; ?></td>
			<td><?php echo $carrier['srv_name'].' ('.$carrier['srv_code'].')'; ?></td>
			<td><?php echo '<u>Label</u> : '.$carrier['label_store'].'<br/><u>Description</u> : '.$carrier['description'].' ('.$carrier['description_store'].')'; ?></td>
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
?>
 
