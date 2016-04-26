<?php
/* Example of use for EnvListPoints class  
 * Load a list of parcel points around a given address
 */ 
$folder = '../';
require_once('../utils/header.php');
require_once('../utils/config.php');
require_once('../env/WebService.php');
require_once('../env/ListPoints.php');

// Prepare and execute the request
$env = 'test';
$locale = 'en-US'; // you can change this to 'fr-FR' or 'es-ES' for instance
$lib = new EnvListPoints($credentials[$env]);
$lib->setEnv($env);
$lib->setLocale($locale);
$params = array(
	'srv_code' => 'RelaisColis',
	'collecte'=> 'exp',
	'pays' => 'FR',
	'cp' => '75011',
	'ville' => 'PARIS'
);
$lib->getListPoints('SOGP', $params);

// Display the parcel points
if(!$lib->curl_error && !$lib->resp_error)
{ 
?>
<style type='text/css'>
	table tr td {border:1px solid #000000; padding:5px; }
</style>
<?php
$week_days = array(
	1 => 'Monday',
	2 => 'Tuesday',
	3 => 'Wednesday',
	4 => 'Thursday',
	5 => 'Friday',
	6 => 'Saturday',
	7 => 'Sunday'
);
?>
<table>
	<tr>
		<td>Code</td>
		<td>Name</td>
		<td>Adress</td>
		<td>City</td>
		<td>Postal code</td>
		<td>Country</td>
		<td>Phone</td>
		<td>Description</td>
		<td>Calendar</td>
	</tr>
<?php	foreach($lib->list_points as $point){	?>
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
						<td>Week day</td>
						<td>Opening am</td>
						<td>Closing am</td>
						<td>Opening pm</td>
						<td>Closing pm</td>
					</tr>
<?php			foreach($point['schedule'] as $day){	?>
						<tr>
							<td><?php echo $week_days[$day['weekday']]; ?></td>
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
handle_errors($lib);
require_once('../utils/footer.php');
?>
 
