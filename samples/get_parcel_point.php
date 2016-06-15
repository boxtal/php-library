<?php
use \Emc\ParcelPoint;

/* Example of use for EnvParcelPoint class
 * Load parcel point informations from their codes
 */

require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');


// Prepare and execute the request
$lib = new ParcelPoint();

// load multiple parcel points
// if you plan to load multiple parcel points around the same address, get_list_parcel_points.php is a better solution
$lib->getParcelPoint("dropoff_point", "SOGP-C1160");
$lib->getParcelPoint("pickup_point", "SOGP-C3183");
$lib->getParcelPoint("pickup_point", "SOGP-C3210");
$lib->getParcelPoint("pickup_point", "SOGP-C3059");
$lib->getParcelPoint("pickup_point", "SOGP-C1250");

// Display loaded parcel points
if (!$lib->curl_error && !$lib->resp_error) {
    $week_days = array( 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
?>
<h3>API ParcelPoint</h3>
<div class="row">
<ul class="list-group">
    <li class="list-group-item active"><b>Pickup points :</b></li>
    <?php
    foreach ($lib->points['pickup_point'] as $point) {
    ?>
    <li class="list-group-item">
        <div class="row">
            <div class="col-xs-6 col-sm-6">
                <b><?php echo $point['name'];?></b> <br />
                <?php echo $point['address'];?>, <?php echo $point['zipcode'];?> <?php echo $point['city'];?>
            </div>
            <div class="col-xs-6 col-sm-6">
                <?php
                ob_start();
                ?>
                <div style='width: 480px;'>
                <table class='table table-striped table-bordered'>
                    <tr>
                        <td>Week day</td>
                        <td>Opening am</td>
                        <td>Closing am</td>
                        <td>Opening pm</td>
                        <td>Closing pm</td>
                    </tr>
                    <?php
                    foreach ($point['schedule'] as $day) {
                    ?>
                    <tr>
                        <td><?php echo $week_days[$day['weekday']]; ?></td>
                        <td><?php echo substr($day['open_am'], 0, 5); ?></td>
                        <td><?php echo substr($day['close_am'], 0, 5); ?></td>
                        <td><?php echo substr($day['open_pm'], 0, 5); ?></td>
                        <td><?php echo substr($day['close_pm'], 0, 5); ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
                </div>
                <?php   $calendar = ob_get_clean(); ?>
                <button type="button" class="btn btn-sm btn-default" data-container="body" data-toggle="popover" data-placement="right" data-content="<?php echo $calendar ; ?>">
                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    Opening time
                </button>
            </div>
        </div>
    </li>
    <?php
    }
    ?>
</ul>
<br /><br />
<ul class="list-group">
    <li class="list-group-item list-group-item-success"><b>Dropoff points :</b></li>
    <?php
    foreach ($lib->points['dropoff_point'] as $point) {
    ?>
    <li class="list-group-item">
        <div class="row">
            <div class="col-xs-6 col-sm-6">
                <b><?php echo $point['name'];?></b> <br />
                <?php echo $point['address'];?>, <?php echo $point['zipcode'];?> <?php echo $point['city'];?>
            </div>
            <div class="col-xs-6 col-sm-6">
                <?php
                ob_start();
                ?>
                <div style='width: 480px;'>
                <table class='table table-striped table-bordered'>
                    <tr>
                        <td>Week day</td>
                        <td>Opening am</td>
                        <td>Closing am</td>
                        <td>Opening pm</td>
                        <td>Closing pm</td>
                    </tr>
                    <?php
                    foreach ($point['schedule'] as $day) {
                    ?>
                    <tr>
                        <td><?php echo $week_days[$day['weekday']]; ?></td>
                        <td><?php echo substr($day['open_am'], 0, 5); ?></td>
                        <td><?php echo substr($day['close_am'], 0, 5); ?></td>
                        <td><?php echo substr($day['open_pm'], 0, 5); ?></td>
                        <td><?php echo substr($day['close_pm'], 0, 5); ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
                </div>
                <?php   $calendar = ob_get_clean(); ?>
                <button type="button" class="btn btn-sm btn-default" data-container="body" data-toggle="popover" data-placement="right" data-content="<?php echo $calendar ; ?>">
                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    Opening time
                </button>
            </div>
        </div>
    </li>
    <?php
    }
    ?>
</ul>
</div>
<?php
} else {
    echo '<div class="alert alert-danger">';
    handle_errors($lib);
    echo'</div>';
}
?>
<div class="well well-sm">
    <button type="button" class="btn btn-xs btn-default" id="toogleDebug">
        Toggle Debug
    </button>
    <pre id="debug" style="display: none">
        <?php print_r(array_merge($lib->getApiParam(), array('API response :' =>$lib->points))); ?>
    </pre>
</div>
<style type="text/css">
    .popover{
        max-width:600px;
    }
</style>
<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');

