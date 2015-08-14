<?php 
/* Example of use for EnvParcelPoint class  
 * Load parcel point informations from their codes
 */ 
 
require_once('../utils/config.php');
require_once('../env/WebService.php');
require_once('../env/ParcelPoint.php');

// Prepare and execute the request
$env = 'test';
$lib = new EnvParcelPoint($credentials[$env]);
$lib->setEnv($env);
// add all the points to the same list
$lib->construct_list = true;
// load multiple parcel points
// if you plan to load multiple parcel points around the same address, get_list_parcel_points.php is a better solution
$lib->getParcelPoint('dropoff_point', 'SOGP-C3084');
$lib->getParcelPoint('dropoff_point', 'SOGP-C3159'); 
$lib->getParcelPoint('dropoff_point', 'SOGP-C3065'); 
$lib->getParcelPoint('dropoff_point', 'SOGP-C3137');  
$lib->getParcelPoint('pickup_point', 'SOGP-C3059'); 
$lib->getParcelPoint('pickup_point', 'SOGP-C3210'); 
$lib->getParcelPoint('pickup_point', 'SOGP-C3138'); 

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
?>  