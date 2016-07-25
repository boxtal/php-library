<?php
use \Emc\ListPoints;

/* Example of use for EnvListPoints class
 * Load a list of parcel points around a given address
 */
require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');


$lib = new ListPoints();

$params = array(
    'collecte'=> 'exp',
    'pays' => 'FR',
    'cp' => '75011',
    'ville' => 'PARIS'
);
$lib->getListPoints(array('SOGP_RelaisColis', 'MONR_CpourToi'), $params);

// Display the parcel points
if (!$lib->curl_error && !$lib->resp_error) {
    $week_days = array( 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
?>
<h3>API ListPoints</h3>
<div class="row">
        <table class="table table-hover table-striped table-bordered">
    <tr>
        <th>Carrier</th>
        <th>Code</th>
        <th>Name</th>
        <th>Address</th>
        <th>City</th>
        <th>Postal code</th>
        <th>Country</th>
        <th>Phone</th>
        <th>Description</th>
        <th>Calendar</th>
    </tr>
<?php   foreach ($lib->list_points as $carrier) {   ?>
    <?php   foreach ($carrier['points'] as $point) {  ?>
        <tr>
            <td><?php echo $carrier['operator'].' '.$carrier['service']; ?></td>
            <td><?php echo $point['code']; ?></td>
            <td><?php echo $point['name']; ?></td>
            <td><?php echo $point['address']; ?></td>
            <td><?php echo $point['city']; ?></td>
            <td><?php echo $point['zipcode']; ?></td>
            <td><?php echo $point['country']; ?></td>
            <td><?php echo $point['phone']; ?></td>
            <td><?php echo $point['description']; ?></td>
            <td>
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
                <?php
                $calendar = ob_get_clean();
                ?>
                <button type="button" class="btn btn-sm btn-default" data-container="body" data-toggle="popover" data-placement="left" data-content="<?php echo $calendar ; ?>">
                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    Opening time
                </button>
            </td>
        </tr>
<?php
}
}
?>
</table>
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
        <?php print_r(array_merge($lib->getApiParam(), array('API response :' =>$lib->list_points))); ?>
    </pre>
</div>
<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');
?>
<style type="text/css">
    .popover{
        max-width:600px;
    }
</style>
