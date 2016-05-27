<?php
use \Emc\CarriersList;

/* Example of use for CarriersList class
 * Get all available carriers for a platform
 * Note that you need this request only if you plan to develop your own platform or module.
 */
require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');

$family = array(
    '1' => '<span class="label label-warning">Economy</span>',
    '2' => '<span class="label label-danger">Express</span>'
);
$zone = array(
    '1' => '<span class="label label-success">FR</span>',
    '2' => '<span class="label label-primary">INTER</span>',
    '3' => '<span class="label label-info">EU</span>',
    'zone_fr' => ' <span class="label label-primary">FR</span>',
    'zone_eu' => ' <span class="label label-info">EU</span>',
    'zone_int' => ' <span class="label label-default">INTER</span>'
);

/* Prepare and execute the request */
$lib = new CarriersList();

$module_version = '3.0.0';
$module_platform = 'prestashop';
$lib->getCarriersList($module_platform, $module_version);

/* Show an array with carrier's informations */
if (!$lib->curl_error && !$lib->resp_error) {
?>
<div class="row">
    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th>Operator</th>
                <th>Service</th>
                <th>Delivery time</th>
                <th>Specifications</th>
                <th>Family</th>
                <th>Zone</th>
                <th>Drop-off</th>
                <th>Pick-up</th>
            </tr>
        </thead>
<?php
foreach ($lib->carriers as $carrier) {
    if (!isset($tmpCarrier)) {
        $tmpCarrier = $carrier['ope_code'];
        $border = "blDefault";
    } elseif ($tmpCarrier != $carrier['ope_code']) {
        $border = ( $border == "blDefault" ? "blActive" : "blDefault");
        $tmpCarrier = $carrier['ope_code'];
    }
    ?>
        <tr>
            <td class="strong <?php echo $border; ?>">
                <span data-container="body" data-toggle="popover" data-placement="bottom" data-content="Operator code : <?php echo $carrier['ope_code']; ?>">
                    <?php echo $carrier['ope_name']; ?>
                </span>
            </td>
            <td>
                <span data-container="body" data-toggle="popover" data-placement="bottom" data-content="Service code : <?php echo $carrier['srv_code']; ?>">
                    <?php echo $carrier['srv_name_bo']; ?>
                </span>
            </td>
            <td>
                <span data-container="body" data-toggle="popover" data-placement="bottom" data-content="<?php echo $carrier['description']; ?>">
                    <?php echo $carrier['delivery_due_time']; ?>
                </span>
            </td>
            <td>
                <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="bottom" data-content="- <?php echo implode('<br/>- ', $carrier['details']); ?>">
                    <span class="glyphicon glyphicon-list" aria-hidden="true"></span> Details
                </button>
            </td>
            <td><?php echo $family[$carrier['family']]; ?></td>
            <td>
                <?php
                      $allZones  = ( $carrier['zone_fr']  == '1'  ?  $zone['zone_fr']  : '');
                      $allZones .= ( $carrier['zone_eu']  == '1'  ?  $zone['zone_eu']  : '');
                      $allZones .= ( $carrier['zone_int'] == '1'  ?  $zone['zone_int'] : '');
                     echo $allZones;
                if (!empty($carrier['zone_restriction'])) {
                ?>
                <span class="glyphicon glyphicon-info-sign" data-container="body" data-toggle="popover" data-placement="bottom" data-content="<?php echo $carrier['zone_restriction']; ?>">
                </span>
                <?php
                } ?>
            </td>
            <td>
                <span class="badge alert-<?php echo $carrier['parcel_pickup_point']=='1'? 'info':'success'; ?>">
                <span class="glyphicon <?php echo $carrier['parcel_pickup_point']=='1'? 'glyphicon-map-marker':'glyphicon-home'; ?>  mr5"></span>
                    <?php echo $carrier['pickup_place']; ?>
                </span>
            </td>
            <td>
                <span class="badge alert-<?php echo $carrier['parcel_dropoff_point']=='1'? 'info':'success'; ?>">
                    <span class="glyphicon <?php echo $carrier['parcel_dropoff_point']=='1'? 'glyphicon-map-marker':'glyphicon-home'; ?>  mr5"></span>
                    <?php echo $carrier['dropoff_place']; ?>
                </span>
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
?>
<div class="well well-sm">
    <button type="button" class="btn btn-xs btn-default" id="toogleDebug">
        Toggle Debug
    </button>
    <pre id="debug" style="display: none">
        <?php print_r(array_merge($lib->getApiParam(), array('API response :' =>$lib->carriers))); ?>
    </pre>
</div>
<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');
