<?php
use \Emc\CarriersList;

/* Example of use for CarriersList class
 * Get all available carriers for a platform
 * Note that you need this request only if you plan to develop your own platform or module.
 */
require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');

$locale = 'en_US';

$family = array(
    '1' => '<span class="label label-warning">Economy</span>',
    '2' => '<span class="label label-danger">Express</span>'
);
$zone = array(
    '1' => '<span class="label label-success">FR</span>',
    '2' => '<span class="label label-primary">INTER</span>',
    '3' => '<span class="label label-info">EU</span>',
    'zone_fr' => ' <span class="label label-primary">FR</span>',
    'zone_es' => ' <span class="label label-primary">ES</span>',
    'zone_eu' => ' <span class="label label-info">EU</span>',
    'zone_int' => ' <span class="label label-default">INTER</span>'
);

/* Prepare and execute the request */
$lib = new CarriersList();

$lib->getCarriersList();

/* Show an array with carrier's informations */
if (!$lib->curl_error && !$lib->resp_error) {
?>
<script type="text/javascript">
$(document).ready(function(){
  $("select.content").change(function(){
    $(this).parent().find(".content_info").html($(this).find(":selected").attr("data-desc"));
  });
  $("select.content").trigger("change");
  
  $(document).on('click', '.iframe', function(e) {
    e.preventDefault();
    var src = $(this).attr('data-src');
    var height = $(this).attr('data-height');
    var width = $(this).attr('data-width');

    $("#emc-modal iframe").attr({
        'src':src,
        'height': height,
        'width': width
    });
  });
});
</script>
<style>
.content_info{
  display:inline-block;
  width:200px;
}
.modal-dialog{
    width:1030px;
}
</style>
<h3>API CarriersList :</h3>
<div class="row">
    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th>Operator</th>
                <th>Service</th>
                <th>Delivery time</th>
                <th>Specifications</th>
                <th>Allowed contents</th>
                <th>Family</th>
                <th>Zone</th>
                <th>Drop-off</th>
                <th>Pick-up</th>
            </tr>
        </thead>
<?php
foreach ($lib->carriers as $i => $carrier) {
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
                (<a data-src="<?php echo $carrier['ope_cgv']; ?>" class="iframe" data-toggle="modal" data-height=600 data-width=1000 data-target="#emc-modal">cgv</a>)
            </td>
            <td>
                <span data-container="body" data-toggle="popover" data-placement="bottom" data-content="Service code : <?php echo $carrier['srv_code']; ?>">
                    <?php echo $carrier['srv_name_bo']; ?>
                </span>
                (<a data-src="<?php echo $carrier['srv_cgv']; ?>" class="iframe" data-toggle="modal" data-height=600 data-width=1000 data-target="#emc-modal">cgv</a>)
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
            <td>
              <select class="content" style="max-width: 200px;">
                <?php
                foreach($carrier['allowed_content'] as $id => $allowed_content) {
                  $desc = '';
                  if ($allowed_content['condition'] != null) {
                    $desc .= '<b>Condition</b> : ' . $allowed_content['condition'];
                  }
                  echo '<option data-desc="'.$desc.'" value="' . $id . '">' . $allowed_content['label'] . '</option>';
                }
                ?>
              </select><br/>
              <span class="content_info" style="width: 200px;"></span>
            </td>
            <td><?php echo $family[$carrier['family']]; ?></td>
            <td>
                <?php
                      $allZones  = ( $carrier['zone_fr']  == '1'  ?  $zone['zone_fr']  : '');
                      $allZones .= ( $carrier['zone_es']  == '1'  ?  $zone['zone_es']  : '');
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
                <span class="badge alert-<?php echo $carrier['parcel_dropoff_point']=='1'? 'info':'success'; ?>">
                    <span class="glyphicon <?php echo $carrier['parcel_dropoff_point']=='1'? 'glyphicon-map-marker':'glyphicon-home'; ?>  mr5"></span>
                    <?php echo $carrier['dropoff_place']; ?>
                </span>
            </td>
            <td>
                <span class="badge alert-<?php echo $carrier['parcel_pickup_point']=='1'? 'info':'success'; ?>">
                <span class="glyphicon <?php echo $carrier['parcel_pickup_point']=='1'? 'glyphicon-map-marker':'glyphicon-home'; ?>  mr5"></span>
                    <?php echo $carrier['pickup_place']; ?>
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
<div class="modal fade" id="emc-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <iframe frameborder="0"></iframe>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');
