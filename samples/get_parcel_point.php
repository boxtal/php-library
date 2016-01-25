<?php 
/* Example of use for EnvParcelPoint class  
 * Load parcel point informations from their codes
 */
$folder = '../';
require_once('../utils/header.php');
require_once('../utils/config.php');
require_once('../env/WebService.php');
require_once('../env/ParcelPoint.php');

// Prepare and execute the request
$env = 'test';
$locale = 'en-US'; // you can change this to 'fr-FR' or 'es-ES' for instance
$lib = new EnvParcelPoint($credentials[$env]);
$lib->setEnv($env);
$lib->setLocale($locale);
// add all the points to the same list
$lib->construct_list = true;
// load multiple parcel points
// if you plan to load multiple parcel points around the same address, get_list_parcel_points.php is a better solution
$lib->getParcelPoint("dropoff_point", "SOGP-C1160");
$lib->getParcelPoint("pickup_point", "SOGP-C3183"); 
$lib->getParcelPoint("pickup_point", "SOGP-C3210"); 
$lib->getParcelPoint("pickup_point", "SOGP-C3059");  
$lib->getParcelPoint("pickup_point", "SOGP-C1250"); 

// Display loaded parcel points
if(!$lib->curl_error && !$lib->resp_error)
{
?>
<p><b>Pickup points :</b></p>
<ul>
<?php foreach($lib->points['pickup_point'] as $point) { ?>
  <li><?php echo $point['name'];?> <br /><?php echo $point['address'];?>, <?php echo $point['zipcode'];?> <?php echo $point['city'];?></li>
<?php } ?>
</ul>
<br /><br />
<p><b>Dropoff points:</b></p>
<ul>
<?php foreach($lib->points['dropoff_point'] as $point) { ?>
  <li><?php echo $point['name'];?> <br /><?php echo $point['address'];?>, <?php echo $point['zipcode'];?> <?php echo $point['city'];?></li>
<?php } ?>
</ul>
<?php
}

handle_errors($lib);
require_once('../utils/footer.php');
?>  