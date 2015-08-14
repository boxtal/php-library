<?php  
/* Example of use for EnvQuotation class  
 * Get all available offers for your send
 * You can find more informations about quotation's request here : http://ecommerce.envoimoinscher.com/api/documentation/cotations/
 */
 
require_once('../utils/config.php');
require_once('../env/WebService.php');
require_once('../env/Quotation.php');

// shipper and recipient's address
$from = array(
	'pays' => 'FR', 
	'code_postal' => '75002',   
	'ville' => 'Paris', 
	'type' => 'entreprise', 
	'adresse' => '41, rue Saint Augustin'
); 
$to = array(
	'pays' => 'FR', 
	'code_postal' => '75002', 
	'ville' => 'Paris', 
	'type' => 'particulier', 
	'adresse' => '41, rue Saint Augustin'
);
	
/*
 * $quot_params contains all additional parameters for your request, it includes filters or offer's options 
 * A list of all possible parameters is available here : http://ecommerce.envoimoinscher.com/api/documentation/commandes/
 */
$quot_params = array(
	'collecte' => date("Y-m-d"),
	'delay' => 'aucun',
	'content_code' => 10120
);
	
// Prepare and execute the request
$env = 'test';
$lib = new EnvQuotation($credentials[$env]);
$lib->setPerson('shipper', $from);
$lib->setPerson('recipient', $to);
$lib->setEnv($env); 
$lib->setType(
	'colis', 
	array(
		1 => array(
			'poids' => 1, 
			'longueur' => 18, 
			'largeur' => 18,
			'hauteur' => 18
		)
	)
);
$lib->getQuotation($quot_params);
$lib->getOffers();

if(!$lib->curl_error && !$lib->resp_error)
{
?>
<style type="text/css">
table tr td {border:1px solid #000000; padding:5px; }
</style>
<table>
	<thead>
		<tr>
			<td>Operator / Service</td>
			<td>Price</td>
			<td>Collect</td>
			<td>Delivery</td>
			<td>DétaDetailsils</td>
			<td>Alert</td>
			<td>Mandatory informations</td>
		</tr>
	</thead>
	<tbody>
<?php foreach($lib->offers as $o => $offre) { ?>
			<tr>
				<td><b><?php echo $o;?></b>. <?php echo $offre['operator']['label'];?> / <?php echo $offre['service']['code'];?></td>
				<td><?php echo $offre['price']['tax-exclusive'];?> <?php echo $offre['price']['currency'];?></td>
				<td><?php echo $offre['collection']['type'];?></td>
				<td><?php echo $offre['delivery']['type'];?></td>
				<td><?php echo implode('<br /> - ', $offre['characteristics']); ?></td>
				<td><?php echo $offre['alert']; ?></td>
				<td><?php foreach($offre['mandatory'] as $m => $mandatory) { ?> - <?php echo $m; ?><br /><?php } ?></td>
			</tr>
<?php } ?>
	</tbody>
</table>
<?php
}

handle_errors($lib);
?>