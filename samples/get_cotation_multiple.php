<?php
use \Emc\Quotation;

/* Example of use for QuotationMulti class
 * Used to ship products from multiple warehouses
 * Get all available offers for your send
 * You can find more informations about quotation's request here : http://ecommerce.envoimoinscher.com/api/documentation/cotations/
 */
require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');

/* for multi quotations, all params are set in a single array with a numeric index from 0.
 * If correctly set, the request response index will be the same as the request numeric index.
 */
$multirequest = array();

// 1st request
$multirequest[0] = array(
    'from' => array(
        'pays' => 'FR', // must be an ISO code, set get_country example on how to get codes
        'code_postal' => '38400',
        'ville' => "Saint Martin d'Hères",
        'type' => 'entreprise',
        'adresse' => '13 rue Martin Luther King'
    ),
    'to' => array(
        'pays' => 'FR', // must be an ISO code, set get_country example on how to get codes
        'code_postal' => '33000',
        'ville' => 'Bordeaux',
        'type' => 'particulier', // accepted values are "entreprise" or "particulier"
        'adresse' => '24, rue des Ayres'
    ),
    'parcels' => array(
        'type' => 'colis', // your shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)
        'dimensions' => array(
            1 => array(
                'poids' => 1,
                'longueur' => 15,
                'largeur' => 16,
                'hauteur' => 8
            )
        )
    ),
    'additional_params' => array(
        'collecte' => date("Y-m-d"),
        'delay' => 'aucun',
        'content_code' => 10120, // List of the available codes at samples/get_categories.php > List of contents
        'valeur' => "42.655"
    )
);

// 2nd request
$multirequest[1] = array(
    'from' => array(
        'pays' => 'FR', // must be an ISO code, set get_country example on how to get codes
        'code_postal' => '75002',
        'ville' => "Paris",
        'type' => 'entreprise',
        'adresse' => '15 rue Marsollier'
    ),
    'to' => array(
        'pays' => 'FR', // must be an ISO code, set get_country example on how to get codes
        'code_postal' => '33000',
        'ville' => 'Bordeaux',
        'type' => 'particulier', // accepted values are "entreprise" or "particulier"
        'adresse' => '24, rue des Ayres'
    ),
    'parcels' => array(
        'type' => 'colis', // your shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)
        'dimensions' => array(
            1 => array(
                'poids' => 1,
                'longueur' => 15,
                'largeur' => 16,
                'hauteur' => 8
            )
        )
    ),
    'additional_params' => array(
        'collecte' => date("Y-m-d"),
        'delay' => 'aucun',
        'content_code' => 10120, // List of the available codes at samples/get_categories.php > List of contents
        'valeur' => "42.655"
    )
);

$currency = array('EUR' => '€', 'USD'=>'$');

// Prepare and execute the request
$lib = new Quotation();

$lib->getQuotationMulti($multirequest);

if (!$lib->curl_error) {
?>
<h3>API Quotation :</h3>
    <div class="row">
        <table class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th>Operator</th>
                    <th>Offers</th>
                    <th>Price</th>
                    <th>Collect</th>
                    <th>Delivery</th>
                    <th>Details</th>
                    <th>Warning</th>
                    <th>Mandatory informations</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($multirequest as $requestIndex => $request) {
                ?>
                    <tr><td colspan="8" class="h4">Quotation n° <?php echo ( $requestIndex +1 ).' - From '.$request['from']['ville'].' to '.$request['to']['ville']; ?></td></tr>
                <?php
                if (isset($lib->resp_errors_list[$requestIndex])) {
                    // case resp error
                    echo '<tr><td class="alert alert-danger" colspan="8">';
                    $text = '';
                    foreach ($lib->resp_errors_list[$requestIndex] as $e => $error) {
                        if ($e == 0) {
                            $text .= 'invalid request : ';
                        } else {
                            $text .= '<br/>';
                        }
                        $text .= $error['message'].' ('.$error['code'].')';
                    }
                    echo $text.'</td></tr>';
                } else {
                    foreach ($lib->offers[$requestIndex] as $offre) {
                                $border = ( $requestIndex % 2 ? "blActive" : "blDefault");
?>
                        <tr >
                            <td class="<?php echo $border; ?>"><?php echo $offre['operator']['label'];?></td>
                            <td><?php echo $offre['operator']['code'].$offre['service']['code'];?></td>
                            <td>
                            <span class="badge alert-default">
                            <?php echo $offre['price']['tax-exclusive'];?> <?php echo (isset($currency[$offre['price']['currency']]) ? $currency[$offre['price']['currency']] : $offre['price']['currency'] ) ;?></td>
                            </span>
                            <td>
                                <span class="badge alert-<?php echo $offre['collection']['type']== 'DROPOFF_POINT' ? 'info':'success'; ?>">
                                <span class="glyphicon <?php echo $offre['collection']['type']== 'DROPOFF_POINT'? 'glyphicon-map-marker':'glyphicon-home'; ?>  mr5"></span>
                                    <?php echo $offre['collection']['type'];?>
                                </span>
                            </td>
                            <td>
                                <span class="badge alert-<?php echo $offre['delivery']['type']== 'PICKUP_POINT' ? 'info':'success'; ?>">
                                <span class="glyphicon <?php echo $offre['delivery']['type']== 'PICKUP_POINT'? 'glyphicon-map-marker':'glyphicon-home'; ?>  mr5"></span>
                                    <?php echo $offre['delivery']['type'];?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="left" data-content="<?php echo str_replace('"', "'", implode('<br /> - ', $offre['characteristics'])); ?>">
                                    <span class="glyphicon glyphicon-list" aria-hidden="true"></span>
                                    Details
                                </button>

                            </td>
                            <td>
                                <button type="button" class="btn btn-xs btn-warning" data-container="body" data-toggle="popover" data-placement="left" data-content="<?php echo $offre['alert']; ?>">
                                    <span class="glyphicon glyphicon-warning-sign"></span>
                                    Warning
                                </button>

                            </td>
                            <td>
                                <button type="button" class="btn btn-xs btn-danger" data-container="body" data-toggle="popover" data-placement="left" data-content="<?php echo '- '. str_replace('"', "'", implode('<br /> - ', array_keys($offre['mandatory']))); ?>">
                                    <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
                                    Mandatory informations
                                </button>
                            </td>
                        </tr>
<?php
                    }
                }
            }
?>
            </tbody>
        </table>
    </div>
<?php
}
require_once(EMC_PARENT_DIR.'layout/footer.php');
