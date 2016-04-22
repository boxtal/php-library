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
$module_platform = 'api';
$module_version = '1.0.0';
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
		<td>Description</td>
		<td>Specifications</td>
		<td>Family</td>
		<td>Zone</td>
		<td>Parcel point drop-off</td>
		<td>Parcel point pick-up</td>
		<td>Delivery time</td>
	</tr>
<?php	foreach($lib->carriers as $carrier){	?>
		<tr>
			<td>
                <?php
                    echo $carrier['ope_name'].'<br/>';
                    echo '<u>code:</u> '.$carrier['ope_code'];
                ?>
            </td>
            <td>
                <?php
                    echo $carrier['srv_name_bo'].'<br/>';
                    echo '<u>code:</u> '.$carrier['srv_code'];
                ?>
            </td>
			<td><?php echo $carrier['description']; ?></td>
			<td>
                <?php 
                    foreach ($carrier['details'] as $detail) {
                        echo $detail.'<br/>';
                    }
                ?>
            </td>
			<td><?php echo $family[$carrier['family']]; ?></td>
			<td>
                <?php 
                    if ($carrier['zone_fr']) {
                        echo 'France';
                    };
                    if ($carrier['zone_eu']) {
                        if ($carrier['zone_fr']) {
                            echo '<br/>';
                        }
                        echo 'Europe';
                    };
                    if ($carrier['zone_int']) {
                        if ($carrier['zone_fr'] || $carrier['zone_eu']) {
                            echo '<br/>';
                        }
                        echo 'International';
                    };
                    if ($carrier['zone_restriction']) {
                        echo '<br/>('.$carrier['zone_restriction'].')';
                    };
                ?>
            </td>
			<td>
                <?php
                    echo $carrier['parcel_pickup_point']=='1'?'Yes':'No';
                    echo ' ('.$carrier['pickup_place'].')';
                ?>
            </td>
			<td>
                <?php 
                    echo $carrier['parcel_dropoff_point']=='1'?'Yes':'No';
                    echo ' ('.$carrier['dropoff_place'].')';
                ?>
            </td>
            <td><?php echo $carrier['delivery_due_time']; ?></td>
		</tr>
<?php	}	?>
</table>
<?php
}

handle_errors($lib);
require_once('../utils/footer.php');
?>
 
