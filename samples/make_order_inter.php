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
    'country' => 'US',  // must be an ISO code, set get_country example on how to get codes
    'state' => 'NY',
    'zipcode' => '10036',
    'city' => 'New York',
    'address' => '123 W 43rd St',
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
    'content_code' => 10120,  // List of the available codes at samples/get_categories.php > List of contents
    'reason' => 'sale', // for a list of authorized values see $ship_reasons (right-hand side values) in Quotation.php
    'colis.valeur' => 1200, // prefixed with your shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)
    'assurance.selection' => false,  // whether you want an extra insurance or not
    'colis.description' => 'Newspapers',
    // you can find more informations about what is sent on this url here : http://ecommerce.envoimoinscher.com/api/documentation/url-de-push
    'url_push' => 'www.my-website.com/push.php&order=N',
    'disponibilite.HDE' => '09:00', // Starting time at which you are available for the pickup
    'disponibilite.HLE' => '19:00', // Ending time at which you are available for the pickup
    'operator' => 'UPSE',
    'service' => 'ExpressSaver'
    // for insurance params, see http://ecommerce.envoimoinscher.com/api/documentation/commandes/
);

// Initialize request
$lib = new Quotation();


// For an international delivery, you must specify the proforma
$lib->setProforma(
    array(
        1 => array(
            'description_en' => 'L\'Equipe newspaper from 1998', // description in english
            'description_fr' => 'le journal L\'Equipe du 1998', // description in french (you can fill it in english if you don't speak french)
            'number' => 1, // number of items
            'value' => 1200, // value of 1 item
            'origine' => 'FR', // country of origin
            'poids' => 4.9 // weight (total weight of product must be inferior to parcel weight)
        )
    )
);
/* if you're sending more parcels
$lib->setProforma(
    array(
        1 => array(
            'description_en' => 'L\'Equipe newspaper from 1998',
            'description_fr' => 'le journal L\'Equipe du 1998',
            'nombre' => 1,
            'valeur' => 10,
            'origine' => 'FR',
            'poids' => 1.2
        ),
        2 => array(
            'description_en' => '300 editions of L\'Equipe newspaper from 1999',
            'description_fr' => '300 numéros de L\'Equipe du 1999',
            'nombre' => 300,
            'valeur' => 8,
            'origine' => 'FR',
            'poids' => 0.1
        )
    )
);
*/

// Execute the request
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
