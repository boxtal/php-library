<?php
use \Emc\ListPoints;

/* Example of use for EnvListPoints class
 * Load a list of parcel points around a given address
 */
require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');


$lib = new ListPoints();

$params = array(
    'srv_code' => 'RelaisColis',
    'collecte'=> 'exp',
    'pays' => 'FR',
    'cp' => '75011',
    'ville' => 'PARIS'
);
$lib->getListPoints('SOGP', $params);

// Display the parcel points
if (!$lib->curl_error && !$lib->resp_error) {
?>
<div class="row">
        <table class="table table-striped table-bordered">
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
<?php   foreach ($lib->list_points as $point) {   ?>
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
                    foreach ($point['days'] as $day) {
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
<?php   }   ?>
</table>
</div>
<?php
} else {
    echo '<div class="alert alert-danger">';
    handle_errors($lib);
    echo'</div>';
}

require_once(EMC_PARENT_DIR.'layout/footer.php');
?>
<script type="text/javascript">
$(document).ready(function() {
    $('[data-toggle="popover"]').popover({ html : true, trigger: 'hover'});
});
</script>

<style type="text/css">
    .popover{
        max-width:600px;
    }
</style>