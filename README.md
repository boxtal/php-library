# Boxtal PHP Library

This PHP library aims to present the PHP implementation of the Boxtal v1 API.

---

### Installation

To install the library, simply:
```
$ composer require boxtale/php-library
```

### Requirements

The PHP library works with PHP 5.4 and upwards.

In order to use the API, you need to create a (free) user account on [Boxtal](https://redirect.boxtal.com/iam/redirect/register?profile=developer).
For testing, you can create a [free test account](https://redirect.boxtal.build/iam/redirect/register?profile=developer).

### Library content

The package contains 5 main directories:
- Emc - contains classes that allow interaction with the API
- config - contains the config and autoload files
- samples - contains files with examples of using the library
- test - a file that tests whether your development environment has all the extensions used by the library

### Quick Start and Examples

First, fill in your credentials and working environnement in the config/config.php file.

```php

define("EMC_MODE", "test");
if (EMC_MODE == "prod") {
    define("EMC_USER", "myLogin");
    define("EMC_PASS", "myPassword");
} else {
    define("EMC_USER", "myLogin");
    define("EMC_PASS", "myPassword");
}
```

##### Get a quotation

Here are the elements needed to get a quotation:
- Your shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)
- Your content type id
- The sender's country, city, address and type (company or individual)
- The recipient's country, city and type (company or individual)
- The collection date (sundays and holidays excluded)
- Your shipment content value (for a cross-boarder quotation)


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

##### Make an order

The process of making an order is the same as making a quotation. The only difference is the extra parameters you need to send.

For the sender and the recipient, you need to give phone numbers, name and first name.
For the shipment, depending on the carrier chosen,you might need to give hours for pickup availability, dropoff and/or pickup parcel points.
All international shipments need an <em>object.</em>valeur parameter (where <em>object</em> is the shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)).

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


// Recipient address
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

/* Parcel information */
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


##### Get the list of available content types

Using the API, you can get a list of the available content types which you will be able to use in your module. The "content type" is the nature of the content that you are shipping.

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

Orders shipping with the Boxtal API use country ISO codes. For now, the system only allows shipments from France to abroad, not from abroad to France. Here is how to get the list of countries.

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
