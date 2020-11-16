# Boxtal PHP Library

This PHP library aims to present the PHP implementation of the EnvoiMoinsCher.com API.

---

### Installation

To install the library, simply:

    $ composer require boxtale/php-library


### Requirements

Boxtal PHP Library works with PHP 5.4, 5.5, 5.6, 7.0.

In order to use the API, you need to create a (free) user account on https://www.boxtal.com, then generate an api key from the account management interface."

### Library content

The package contains 5 main directories:
  * Emc - contains classes that allow interaction with the API
  * ca - contains the certificate required for communication with the API
  * config - contains the config and autoload files
  * samples - contains files with examples of using the library
  * test - a file that tests whether your development environment has all the extensions used by the library


### Quick Start and Examples

First, fill in your credentials and working environnement in the config/config.php file.

```php

define("EMC_MODE", "test");
if (EMC_MODE == "prod") {
    define("EMC_USER", "myLogin");
    define("EMC_PASS", "myPassword");
    define("EMC_KEY", "myAPIkeyProd");
} else {
    define("EMC_USER", "myLogin");
    define("EMC_PASS", "myPassword");
    define("EMC_KEY", "myAPIkeyTest");
}

```


##### 1. Signup to boxtal.com

To create a (free) Boxtal user account, you have two options:
  * Either on <a href="https://www.boxtal.com/fr/fr/app/utilisateur/creation-compte" target="_blank">www.boxtal.com</a>, then generate an api key from the account management interface."
  * Or using the postUserSignup method available in EnvoiMoinsCher API. In that case, you will receive an email confirming that your account was successfully created and another email with your API keys.</p>


```php

    require __DIR__ . '/vendor/autoload.php';

    // Params to create account as Professional
    $params =array(
        'facturation.contact_ste'=>'Boxtal', // maxlength=30
        'facturation.contact_civ'=>'M.', // Accepted values are "M." (sir) or "Mme" (madam), maxlength=3
        'facturation.contact_nom'=>'Snow', // maxlength=20
        'facturation.contact_prenom'=>'Jon', // maxlength=20
        'facturation.adresse1'=>'15 rue Marsollier', // maxlength=30
        'facturation.adresse2'=>'', // Address line 2 , maxlength=30
        'facturation.adresse3'=>'', // Address line 3 , maxlength=30
        'facturation.ville'=>'Paris', // City , maxlength=50
        'facturation.pays_iso'=>'FR', // Country ISO code, maxlength=2
        'facturation.codepostal'=>'75001', // maxlength=72
        'facturation.contact_email'=>'jsnow@boxtal.com', //maxlength=255
        'facturation.contact_tel'=>'0606060606', // maxlength=17
        'facturation.contact_locale'=>'fr_FR', // maxlength=5
        'facturation.defaut_enl'=>'on', // Set the adress as default collect adress, maxlength=3
        'facturation.contact_stesiret'=>'12345678912345', // SIRET, maxlength=14
        'facturation.contact_tvaintra'=>'123456', // Intra-community VAT No,  maxlength=50

        'moduleEMC'=>'on', // To obtain an API key, maxlength=3
        'user.login'=>'jsnow', // maxlength=50
        'user.password'=> urlencode($lib->encryptPassword('password')), // Encrypted password, minlength=6

        //Optional
        'user.profession'=>'gerant', // Your title, (gerant, developpeur, agence, free-lance, autre) , maxlength=30
        'user.partner_code'=>'', // If you have a partner code , maxlength=32
        'user.volumetrie'=>'2', // Your average shipping quantity peer month? 1 (less than 10), 2 (10 to 100), 3 (100 to 250), 4 (250 to 500), 5 (500 to 1000), 6 (1000 to 2000), 7 (2000 to 5000), 8 (5000 to 10000)
        'user.site_online'=>'1', // Is your website online ? (1 (yes), 0 (no))
        'user.logiciel'=>'prestashop-1.6' // Possible values (prestashop-1.5, prestashop-1.6, drupal, magento, woocommerce, oscommerce, oxatis)
    );
    $lib = new \Emc\User();
    
    // Not setting credentials to empty would result in creating a linked account to the parent credentials
    $lib->setLogin('');
    $lib->setPassword('');
    $lib->setKey('');

    // Setting environment to 'prod' will create a valid account with test and production API keys
    // Creating an account in a 'test' environment would result in an incomplete account
    $lib->setEnv('prod');
    
    $response = $lib->postUserSignup($params);


```


##### 2. Get a quotation

Here are the elements needed to get a quotation:
  * Your shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)
  * Your content type id
  * The sender's country, city, address and type (company or individual)
  * The recipient's country, city and type (company or individual)
  * The collection date (sundays and holidays excluded)
  * Your shipment content value (for a cross-boarder quotation)


```php
require __DIR__ . '/vendor/autoload.php';

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

$additionalParams = array(
    'collecte' => date("Y-m-d"),
    'delay' => 'aucun',
    'content_code' => 10120,
    'valeur' => "42.655"
);

$lib = new \Emc\Quotation();
$lib->getQuotation($from, $to, $parcels, $additionalParams);
// The offers list is available on the array : $lib->offers

if (!$lib->curl_error && !$lib->resp_error) {
    print_r($lib->offers);
} else {
    handle_errors($lib);
}

```

##### 4. Make an order

The process of making an order is the same as making a quotation. The only difference is the extra parameters you need to send.
* _For the sender and the recipient, you need to give phone numbers, name and first name._
* _For the shipment, depending on the carrier chosen,you might need to give hours for pickup availability, dropoff and/or pickup parcel points_
* _All international shipments need an <em>object.</em>valeur parameter (where <em>object</em> is the shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope))._



```php
require __DIR__ . '/vendor/autoload.php';

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

$additionalParams = array(
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
$lib = new \Emc\Quotation();

$orderPassed = $lib->makeOrder($from, $to, $parcels, $additionalParams);

if (!$lib->curl_error && !$lib->resp_error) {
    print_r($lib->order);
} else {
    handle_errors($lib);
}

```


##### 4. Get the list of available content types

>Using the API, you can get a list of the available content types which you will be able to use in your module. The "content type" is the nature of the content that you are shipping.

```php

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

```

##### 5. Get the list of countries

>Orders shipping with the EnvoiMoinsCher API use country ISO codes. For now, the system only allows shipments from France to abroad, not from abroad to France. Here is how to get the list of countries.

```php

$lib = new \Emc\Country();
$lib->getCountries();
// The countries list is available on the array : $lib->countries
if (!$lib->curl_error && !$lib->resp_error) {
    print_r($lib->countries);
} else {
    handle_errors($lib);
}

```
