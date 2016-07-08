<?php
require_once('config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');
?>
<div>
    <h3>Quick start / Boxtale PHP Library</h3>
    <p>This PHP library aims to present the PHP implementation of the <a href="http://www.envoimoinscher.com" target="_blank">EnvoiMoinsCher.com</a> API.</p>
    <p>We will see step by step the essential blocks for building a custom shipping module on your e-shop:/p>
    <ul class="myTab">
        <li><a href="#cat" role="tab" data-toggle="tab">Available content types</a></li>
        <li><a href="#country" role="tab" data-toggle="tab">Countries list</a></li>
        <li><a href="#cotations" role="tab" data-toggle="tab">Get quotations</a></li>
        <li><a href="#order" role="tab" data-toggle="tab">Make Orders</a></li>
    </ul> 
     <p>For more information on input parameters, classes, changelog, please refer to our <a href="http://ecommerce.envoimoinscher.com/api/documentation/" target="_blank">documentation</a> (in french).</p>
    <br/>
     <h4>Installation.</h4>
        To install Boxtale PHP Library, simply : <br/>
        <b>$ composer require boxtale/php-library </b>
        <br/><br/>
     <h4>Requirements et and general information about the EnvoiMoinsCher API.</h4>
     <p>In order to use the API, you need to create a (free) user account on <a href="http://www.envoimoinscher.com/inscription.html" target="_blank">www.envoimoinscher.com</a>, checking the "I would like to install the EnvoiMoinsCher module directly on my E-commerce website." box. You will then receive an email with your API keys and be able to start your tests.</p>
    Make sure to fill in your credentials in the configuration file : config/config.php
    <pre>
    /* To use 'test' or 'prod' environment  */
    define("EMC_MODE", "test");

    if (EMC_MODE == "prod") {
        /* To set 'prod' environment constants */
        define("EMC_USER", "yourLoginProd");
        define("EMC_PASS", "yourPasswordProd");
        define("EMC_KEY", "yourApiKeyProd");
    } else {
        /* To set 'test' environment constants */
        define("EMC_USER", "yourLoginTest");
        define("EMC_PASS", "yourPasswordTest");
        define("EMC_KEY", "yourApiKeyTest");
    }</pre>

    <br/>
    <h4>Library content</h4>
    <p>The package contains 5 main directories:</p>
    <ul>
        <li>ca - contains the certificate required for communication with the API</li>
        <li>Emc - contains classes that allow interaction with the API</li>
        <li>config - contains the config and autoload files</li>
        <li>samples - contains files with examples of using the library</li>
        <li>test - a file that tests whether your development environment has all the extensions used by the library</li>
    </ul>

    <br/>

<ul class="myTab nav nav-tabs" role="tablist">
    <li class="active"><a href="#cat" role="tab" data-toggle="tab">Available content types</a></li>
    <li><a href="#country" role="tab" data-toggle="tab">Countries list</a></li>
    <li><a href="#cotations" role="tab" data-toggle="tab">Get quotations</a></li>
    <li><a href="#order" role="tab" data-toggle="tab">Make Orders</a></li>
</ul>
<div class="tab-content">
      <div class="tab-pane active" id="cat">
        <h5 id="categories">1. How can I get a list of available content types ?</h5>
        <p>Using the API, you can get a list of the available content types which you will be able to use in your module.
            The "content type" is the nature of the content that you are shipping.</p>
        <pre>
    require __DIR__ . '/vendor/autoload.php';

    $lib = new \Emc\ContentCategory();
    $lib->getCategories(); // load all content categories
    $lib->getContents();   // load all content types

    // The content categories list is available on the array : $lib->categories
    // The content types list is available on the array : $lib->contents

    if (!$lib->curl_error && !$lib->resp_error) {
        print_r($lib->categories);
        print_r($lib->contents);

    } else {
        handle_errors($lib);
    }

        </pre>
        <p>The API will need the content type ids as a parameter for quotations and orders.</p>
        <p>See also: <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_categories.php">list of contents example</a></p>
        <br/>
    </div>
    <div class="tab-pane" id="country">
        <h5 id="countries">2. How can I get a list of countries ?</h5>
        <p>Orders shipping with the EnvoiMoinsCher API use country ISO codes. For now, the system only allows shipments from France to abroad, not from abroad to France. Here is how to get the list of countries:</p>
        <pre>

    $lib = new \Emc\Country();
    $lib->getCountries();
    // The countries list is available on the array : $lib->countries

    if (!$lib->curl_error && !$lib->resp_error) {
        print_r($lib->countries);
    } else {
        handle_errors($lib);
    }
        </pre>
        <p>The API will need the country ISO code as a parameter for several actions.</p>
        <p>See also: <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_country.php">list of countries example</a></p>
        <br/>
    </div>
    <div class="tab-pane" id="cotations">
        <h5 id="quotations">3. How to get a quotation ?</h5>
        <p>Here are the elements needed to get a quotation:</p>
        <ul>
            <li>your shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)</li>
            <li>your content type id</li>
            <li>the sender's country, city, address and type (company or individual)</li>
            <li>the recipient's country, city and type (company or individual)</li>
            <li>the collection date (sundays and holidays excluded)</li>
            <li>your shipment content value (for a cross-boarder quotation)</li>
        </ul>
        <pre>
    // shipper address
    $from = array(
        'pays' => 'FR', // must be an ISO code, set get_country example on how to get codes
        'code_postal' => '38400',
        'ville' => "Saint Martin d'Hères",
        'type' => 'entreprise',
        'adresse' => '13 Rue Martin Luther King'
    );
    // recipient's address
    $to = array(
        'pays' => 'FR', // must be an ISO code, set get_country example on how to get codes
        'code_postal' => '33000',
        'ville' => 'Bordeaux',
        'type' => 'particulier', // accepted values are "entreprise" or "particulier"
        'adresse' => '24, rue des Ayres'
    );


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

    $quot_params = array(
        'collecte' => date("Y-m-d"),
        'delay' => 'aucun',
        'content_code' => 10120, // List of the available codes at samples/get_categories.php > List of contents
        'valeur' => "42.655"
    );

    $lib = new Quotation($from, $to, $parcels);
    $lib->getQuotation($quot_params);
    $lib->getOffers();
    // The offers list is available on the array : $lib->offers

    if (!$lib->curl_error && !$lib->resp_error) {
        print_r($lib->offers);
    } else {
        handle_errors($lib);
    }
        </pre>
        <p>See also: <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_cotation.php">Paris to Bordeaux</a>
        <p>See also: <a href="<?php echo EMC_PARENT_DIR; ?>samples/get_cotation.php?dest=Sydney">Paris to Sydney (international)</a>

        <br/>

    </div>
    <div class="tab-pane" id="order">
        <h5 id="orders">4. How to make an order ?</h5>
        <p>The process of making an order is the same as making a quotation. The only difference is the extra parameters you need to send.<br/>
        For the sender and the recipient, you need to give phone numbers, name and first name.<br/>
        For the shipment, depending on the carrier chosen,
        you might need to give hours for pickup availability, dropoff and/or pickup parcel points.</p>
        <p>All international shipments need an <em>object.</em>valeur parameter (where <em>object</em> is the shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)).</p>
        <pre>

    // shipper address
    $from = array(
        'pays' => 'FR',  // must be an ISO code, set get_country example on how to get codes
        'code_postal' => '75002',
        'ville' => 'Paris',
        'type' => 'entreprise', // accepted values are "particulier" or "entreprise"
        'adresse' => '15, rue Marsollier',
        'civilite' => 'M', // accepted values are "M" (sir) or "Mme" (madam)
        'prenom' => 'John',
        'nom' => 'Snow',
        'societe' => 'Boxtale',
        'email' => 'jsnow@boxtale.com',
        'tel' => '0606060606',
        'infos' => 'Some informations about this address'
    );


    // Recipient's address
    $to = array(
        'pays' => 'FR',  // must be an ISO code, set get_country example on how to get codes
        'code_postal' => '13002',
        'ville' => 'Marseille',
        'type' => 'particulier', // accepted values are "particulier" or "entreprise"
        'adresse' => '1, rue Chape',
        'civilite' => 'Mme', // accepted values are "M" (sir) or "Mme" (madam)
        'prenom' => 'Jane',
        'nom' => 'Doe',
        'email' => 'jdoe@boxtale.com',
        'tel' => '0606060606',
        'infos' => 'Some informations about this address'
    );

    /* Parcels informations */
    $parcels = array(
        'type' => 'colis', // your shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)
        'dimensions' => array(
            1 => array(
                'poids' => 5,
                'longueur' => 15,
                'largeur' => 16,
                'hauteur' => 8
            )
        )
    );

    $quot_params = array(
        'collecte' => date('Y-m-d'),
        'delai' => "aucun",
        'assurance.selection' => false, // whether you want an extra insurance or not
        'url_push' => 'www.my-website.com/push.php&order=',
        'content_code' => 40110,
        'colis.description' => "Tissus, vêtements neufs",
        'valeur' => "42.655",
        'depot.pointrelais' => 'CHRP-POST',
        'operator' => 'CHRP',
        'service' => 'Chrono18'
    );


    // Prepare and execute the request
    $lib = new \emc\Quotation($from, $to, $parcels);

    $orderPassed = $lib->makeOrder($quot_params);

    if (!$lib->curl_error && !$lib->resp_error) {
        print_r($lib->order);
    } else {
        handle_errors($lib);
    }
    </pre>
    </div>
    <br/><br/>
    <p>For more information on input parameters, classes, changelog, please refer to our <a href="http://ecommerce.envoimoinscher.com/api/documentation/" target="_blank">documentation</a> (in french).</p>
    <p>If you have any trouble implementing the library, email us at <a href="mailto:api@envoimoinscher.com">api@envoimoinscher.com</a>.</p>
</div>
<br/><br/>
</div>
<div class="footer">
        <p>&copy; Boxtale 2016</p>
      </div>
<script>
  $(document).ready(function() {
    $('myTab a:first').tab('show')
    });
</script>

<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');
