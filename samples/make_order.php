<?php
use \Emc\Quotation;

/*
 * Make an order
 */
require_once('../config/autoload.php');
require_once('../layout/header.php');


// shipper and recipient's address
$from = array(
    'pays' => 'FR',
    'code_postal' => '75002',
    'ville' => 'Paris',
    'type' => 'particulier',
    'adresse' => '15, rue Marsollier',
    'civilite' => 'M',
    'prenom' => 'Prénom',
    'nom' => 'Nom',
    'email' => 'informationapi@envoimoinscher.com',
    'tel' => '0606060606',
    'infos' => 'Some informations about this address'
);



$dest =  isset($_GET['dest']) ? $_GET['dest'] : null;
switch ($dest) {
    case 'Sydney':
        $to = array(
            "pays" => "AU",
            "code_postal" => "2000",
            "ville" => "Sydney",
            "type" => "particulier",
            "adresse" => "King Street",
            'civilite' => 'M',
            'prenom' => 'John',
            'nom' => 'Snow',
            'email' => 'jsnow@boxtale.com',
            'tel' => '0606060606',
            'infos' => 'Some informations about this address'
         );
        break;
    default:
        $to = array(
            'pays' => 'FR',
            'code_postal' => '28210',
            'ville' => 'lormaye',
            'type' => 'particulier',
            'adresse' => '41, rue Saint Augustin',
            'civilite' => 'M',
            'prenom' => 'John',
            'nom' => 'Snow',
            'email' => 'jsnow@boxtale.com',
            'tel' => '0606060606',
            'infos' => 'Some informations about this address'
        );
        break;
}

/* Parcels informations */
$parcels = array(
    'type' => 'colis',
    'dimensions' => array(
        1 => array(
            'poids' => 5,
            'longueur' => 15,
            'largeur' => 16,
            'hauteur' => 8
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

// Prepare and execute the request
$lib = new Quotation($from, $to, $parcels);


/*
 * $quot_params contains all additional parameters for your request, it includes filters or offer's options
 * A list of all possible parameters is available here : http://ecommerce.envoimoinscher.com/api/documentation/commandes/
 * For an order, you have to provide at least all offer's mandatory parameters returned by the quotation
 * You can also find all optional parameters (filter not included) in the same quotation
 */

switch ($dest) {
    case 'Sydney':
        // For an international send, you must specify the proforma
        $lib->setProforma(
            array(
                1 => array(
                    'description_en' => 'L\'Equipe newspaper from 1998',
                    'description_fr' => 'le journal L\'Equipe du 1998',
                    'nombre' => 1,
                    'valeur' => 1200,
                    'origine' => 'FR',
                    'poids' => 4.9 // Le poids de la marchandise que vous indiquez doit être inférieur au poids de votre envoi
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
        $quot_params = array(
            'collecte' => date('Y-m-d'),
            'delay' => 'aucun',
            'content_code' => 10120,
            'raison' => 'sale', // for a list of authorized values see $ship_reasons (right-hand side values) in Quotation.php
            'colis.valeur' => 1200,
            'assurance.selected' => false,
            'colis.description' => 'Des journaux',
            'disponibilite.HDE' => '09:00',
            'disponibilite.HLE' => '19:00',
            // you can find more informations about what is sent on this url here : http://ecommerce.envoimoinscher.com/api/documentation/url-de-push
            'url_push' => 'www.my-website.com/push.php&order=N',
            // even if these parameters are optional we highly recommend you to set the operator and service you want
            'operator' => 'UPSE',
            'service' => 'ExpressSaver'
        );

        break;
    default:
        $quot_params = array(
            'collecte' => date('Y-m-d'),
            'delay' => 'aucun',
            'content_code' => 40110,
            'colis.description' => "Tissus, vêtements neufs",
            'assurance.selected' => false,
            'depot.pointrelais' => 'MONR-065846', // if not a parcel-point use {operator code}-POST
            'retrait.pointrelais' => 'MONR-079499', // if not a parcel-point use {operator code}-POST
            // you can find more informations about what is sent on this url here : http://ecommerce.envoimoinscher.com/api/documentation/url-de-push
            'url_push' => 'www.my-website.com/push.php&order=',
            'operator' => 'MONR',
            'service' => 'CpourToi'
        );
        break;
}

$orderPassed = $lib->makeOrder($quot_params);

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
