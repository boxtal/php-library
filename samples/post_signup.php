<?php
use \Emc\User;

require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');


$lib = new User();

// Params to create account as Professional
$params =array(
    'facturation.contact_ste'=>'Boxtale',
    'facturation.contact_civ'=>'M.', // Accepted values are "M" (sir) or "Mme" (madam)
    'facturation.contact_nom'=>'Snow',
    'facturation.contact_prenom'=>'Jon',
    'facturation.adresse1'=>'15 rue Marsollier',
    'facturation.adresse2'=>'', // Address line 2
    'facturation.adresse3'=>'', // Address line 3
    'facturation.ville'=>'Paris', // City
    'facturation.pays_iso'=>'FR', // Country ISO code
    'facturation.codepostal'=>'75001',
    'facturation.contact_email'=>'jsnow@boxtale.com',
    'facturation.contact_tel'=>'0606060606',
    'facturation.contact_locale'=>'fr_FR',
    'facturation.defaut_enl'=>'on', // Set the adress as default collect adress
    'facturation.contact_stesiret'=>'12345678912345', // SIRET
    'facturation.contact_tvaintra'=>'123456', // Intra-community VAT No

    'moduleEMC'=>'on', // To obtain an API key within 24 hours.
    'user.login'=>'jsnow',
    'user.password'=> urlencode($lib->encryptPassword('password')),

    //Optional
    'user.profession'=>'gerant', // Your title, (gerant, developpeur, agence, free-lance, autre)
    'user.partner_code'=>'', // If you have a partner code
    'user.volumetrie'=>'2', // Your average shipping quantity peer month? 1 (less than 10), 2 (10 to 100), 3 (100 to 250), 4 (250 to 500), 5 (500 to 1000), 6 (1000 to 2000), 7 (2000 to 5000), 8 (5000 to 10000)
    'user.site_online'=>'1', // Is your website online ? (1 (yes), 0 (no))
    'user.logiciel'=>'prestashop-1.6', // Possible values (prestashop-1.5, prestashop-1.6, drupal, magento, woocommerce, oscommerce, oxatis)
    'user.default_shipping_country'=> 'fr' // To activate local carriers. Possible values: 'fr' (default), 'es'
);

/*
// Params to create account as Private individual
$params =array(
    'facturation.contact_civ'=>'M.', // Accepted values are "M" (sir) or "Mme" (madam)
    'facturation.contact_nom'=>'Snow',
    'facturation.contact_prenom'=>'John',
    'facturation.adresse1'=>'15 rue Marsollier',
    'facturation.adresse2'=>'', // Address line 2
    'facturation.adresse3'=>'', // Address line 3
    'facturation.ville'=>'Paris', // City
    'facturation.pays_iso'=>'FR', // Country ISO code
    'facturation.codepostal'=>'75001',
    'facturation.contact_email'=>'jsnow@boxtale.com',
    'facturation.contact_tel'=>'0606060606',
    'facturation.contact_locale'=>'fr_FR',
    'facturation.defaut_enl'=>'on', // Set the adress as default collect adress

    'user.login'=>'jsnow',
    'user.password'=> urlencode($lib->encryptPassword('password')),
);
*/

// Not setting credentials to empty would result in creating a linked account to the parent credentials
$lib->setLogin('');
$lib->setPassword('');
$lib->setKey('');

// Setting environment to 'prod' will create a valid account with test and production API keys
// Creating an account in a 'test' environment would result in an incomplete account
$lib->setEnv('prod');

$response = $lib->postUserSignup($params);

if (!$lib->curl_error && !$lib->resp_error) {
    if ($response == "1") {
        echo '<div class="alert alert-success">You\'ll receive an e-mail confirming that your account was successfully created and now ready for use.</div>';
    } elseif ($response == "0") {
        echo '<div class="alert alert-warning">An error occurred during account creation ! please try again later.</div>';
    } else {
        echo '<pre class="alert alert-danger">';
        print_r(json_decode($response, true));
        echo'</pre>';
    }
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
        <?php print_r(array_merge($lib->getApiParam(), array('API response :' =>$response))); ?>
    </pre>
</div>
<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');