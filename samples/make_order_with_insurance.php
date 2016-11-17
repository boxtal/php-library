<?php
use \Emc\Quotation;

/*
 * Make an order
 */
require_once('../config/autoload.php');
require_once('../layout/header.php');


// shipper address
$from = array(
    'country' => 'FR',  // must be an ISO code, set get_country example on how to get codes
    // "state" : "" if required, state must be an ISO code as well
    'zipcode' => '75002',
    'city' => 'Paris',
    'address' => '15, rue Marsollier',
    'type' => 'company', // accepted values are "company" or "individual"
    'title' => 'M', // accepted values are "M" (sir) or "Mme" (madam)
    'firstname' => 'Jon',
    'lastname' => 'Snow',
    'societe' => 'Boxtale', // company name
    'email' => 'jsnow@boxtale.com',
    'phone' => '0606060606',
    'infos' => 'Some additional information about this address'
);


// Recipient's address
$to = array(
    'country' => 'FR',  // must be an ISO code, set get_country example on how to get codes
    // "state" : "" if required, state must be an ISO code as well
    'zipcode' => '13002',
    'city' => 'Marseille',
    'address' => '1, rue Chape',
    'type' => 'individual', // accepted values are "company" or "individual"
    'title' => 'Mme', // accepted values are "M" (sir) or "Mme" (madam)
    'firstname' => 'Jane',
    'lastname' => 'Doe',
    'email' => 'jdoe@boxtale.com',
    'phone' => '0606060606',
    'infos' => 'Some additional information about this address'
);

/* Parcels informations */
$parcels = array(
    'type' => 'colis', // your shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)
    'dimensions' => array(
        1 => array(
            'poids' => 5, // parcel weight
            'longueur' => 15, // parcel length
            'largeur' => 16, // parcel width
            'hauteur' => 8 // parcel height
        )
    )
);

/* Optionally you can send two parcels in one order like this
$lib->setType(
    'type' => 'colis',
    'dimensions' => array(
        1 => array('poids' => 21, 'longueur' => 7, 'largeur' => 8, 'hauteur' => 11),
        2 => array('poids' => 15, 'longueur' => 9, 'largeur' => 8, 'hauteur' => 11)
  )
);
*/

/*
 * $additionalParams contains all additional parameters for your request, it includes filters or offer's options
 * A list of all possible parameters is available here : http://ecommerce.envoimoinscher.com/api/documentation/commandes/
 * For an order, you have to provide at least all offer's mandatory parameters returned by the quotation
 * You can also find all optional parameters (filter not included) in the same quotation
 */
$additionalParams = array(
    'collection_date' => date('Y-m-d'),
    'delay' => 'aucun',
    'content_code' => 40110,  // List of the available codes at samples/get_categories.php > List of contents
    'colis.description' => "Clothes", // prefixed with your shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)
    'colis.valeur' => "42.655", // prefixed with your shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)
    // you can find more informations about what is sent on this url here : http://ecommerce.envoimoinscher.com/api/documentation/url-de-push
    'url_push' => 'www.my-website.com/push.php&order=',
    'depot.pointrelais' => 'MONR-000515', // if not a parcel-point use {operator code}-POST like "CHRP-POST"
    'retrait.pointrelais' => 'MONR-087106', // if not a parcel-point use {operator code}-POST like "CHRP-POST"
    'operator' => 'MONR',
    'service' => 'CpourToi',
    // for insurance params, see http://ecommerce.envoimoinscher.com/api/documentation/commandes/
    // from API version > 1.2.0, you have to send ids corresponding to the values sent during quotation
    'assurance.selection' => true,
    'assurance.emballage' => 1,
    'assurance.materiau' => 101,
    'assurance.protection' => 201,
    'assurance.fermeture' => 304
);

// Prepare and execute the request
$lib = new Quotation();

$orderPassed = $lib->makeOrder($from, $to, $parcels, $additionalParams);
echo "<h3>API Quotation > makeOrder :</h3>";
if (!$lib->curl_error && !$lib->resp_error) {
    if ($orderPassed) {
        echo '<div class="alert alert-success"> Order passed with the reference '. $lib->order['ref'] .'</div>';
    } else {
        echo '<div class="alert alert-warning"> Your order has been refused </div>';
    }
} else {
    echo '<div class="alert alert-danger">';
    handle_errors($lib);
    echo'</div>';
}
require_once('../layout/quotation_datails.php');
require_once('../layout/footer.php');
