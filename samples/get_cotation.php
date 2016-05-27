<?php
use \Emc\Quotation;

/* Example of use for Quotation class
 * Get all available offers for your send
 * You can find more informations about quotation's request here : http://ecommerce.envoimoinscher.com/api/documentation/cotations/
 */
require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');


// shipper and recipient's address
$from = array(
    'pays' => 'FR',
    'code_postal' => '38400',
    'ville' => "Saint Martin d'Hères",
    'type' => 'entreprise',
    'adresse' => '13 Rue Martin Luther King'
);

$dest =  isset($_GET['dest']) ? $_GET['dest'] : null;
switch ($dest) {
    case 'Sydney':
        $to = array(
            "pays" => "AU",
            "code_postal" => "2000",
            "ville" => "Sydney",
            "type" => "particulier",
            "adresse" => "King Street"
         );
        break;
    default:
        $to = array(
            'pays' => 'FR',
            'code_postal' => '33000',
            'ville' => 'Bordeaux',
            'type' => 'particulier',
            'adresse' => '24, rue des Ayres'
        );
        break;
}


/*
 * $quot_params contains all additional parameters for your request, it includes filters or offer's options
 * A list of all possible parameters is available here: http://ecommerce.envoimoinscher.com/api/documentation/commandes/
 */
$quot_params = array(
    'collecte' => date("Y-m-d"),
    'delay' => 'aucun',
    'content_code' => 10120,
    'valeur' => "42.655"
);


/* Optionally you can define which carriers you want to quote if you don't want to quote all carriers
$quot_params['offers'] = array(
    0 => 'MONRCpourToi',
    1 => 'SOGPRelaisColis',
    2 => 'POFRColissimoAccess',
    3 => 'CHRPChrono13',
    4 => 'UPSEExpressSaver',
    5 => 'DHLEExpressWorldwide'
);
*/
/* Parcels informations */
$parcels = array(
    'type' => 'colis',
    'dimensions' => array(
        1 => array(
            'poids' => 1,
            'longueur' => 15,
            'largeur' => 16,
            'hauteur' => 8
        )
    )
);
// Prepare and execute the request
$lib = new Quotation($from, $to, $parcels);
$lib->getQuotation($quot_params);
$lib->getOffers();

if (!$lib->curl_error && !$lib->resp_error) {
?>
    <div class="row">
        <table class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <td>Operator</td>
                    <td>Offers</td>
                    <td>Price</td>
                    <td>Collect</td>
                    <td>Delivery</td>
                    <td>Details</td>
                    <td>Warning</td>
                    <td>Mandatory informations</td>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($lib->offers as $offre) { ?>
                    <tr>
                        <td><?php echo $offre['operator']['label'];?></td>
                        <td><?php echo $offre['operator']['code'].$offre['service']['code'];?></td>
                        <td>
                        <span class="badge alert-success">
                        <?php echo $offre['price']['tax-exclusive'];?> <?php echo $offre['price']['currency'];?></td>
                        </span>
                        <td>
                            <span class="btnicon <?php echo $offre['collection']['type'];?>" ></span>
                            <span class="label  <?php echo $offre['collection']['type'];?>">
                                <?php echo $offre['collection']['type'];?>
                            </span>
                        </td>
                        <td>
                            <span class="btnicon <?php echo $offre['delivery']['type'];?>" ></span>
                            <span class="label  <?php echo $offre['delivery']['type'];?>">
                                <?php echo $offre['delivery']['type'];?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-default" data-container="body" data-toggle="popover" data-placement="left" data-content="<?php echo str_replace('"', "'", implode('<br /> - ', $offre['characteristics'])); ?>">
                                <span class="glyphicon glyphicon-list" aria-hidden="true"></span>
                                Details
                            </button>

                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" data-container="body" data-toggle="popover" data-placement="left" data-content="<?php echo $offre['alert']; ?>">
                                <span class="glyphicon glyphicon-warning-sign"></span>
                                Warning
                            </button>

                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" data-container="body" data-toggle="popover" data-placement="left" data-content="<?php echo '- '. str_replace('"', "'", implode('<br /> - ', array_keys($offre['mandatory']))); ?>">
                                <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
                                Mandatory informations
                            </button>
                        </td>
                    </tr>
<?php
            }
?>
            </tbody>
        </table>
    </div>
<?php
} else {
    echo '<div class="alert alert-danger">';
    handle_errors($lib);
    echo'</div>';
}
require_once(EMC_PARENT_DIR.'layout/quotation_datails.php');
?>
<div class="well well-sm">
    <button type="button" class="btn btn-xs btn-default" id="toogleDebug">
        Toggle Debug
    </button>
    <pre id="debug" style="display: none">
        <?php print_r(array_merge($lib->getApiParam(), array('API response :' =>$lib->offers))); ?>
    </pre>
</div>
<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');