<?php   
ob_start();  
header('Content-Type: text/html; charset=utf-8');  
error_reporting(E_ERROR | E_WARNING | E_PARSE); 
require_once $_SERVER['DOCUMENT_ROOT'].'/librairie/utils/autoload.php'; 
  
// création du destinataire et de l'expéditeur - classe séparée car dans l'avenir on voudra 
// peut-être gérer les carnets d'adresses ou gestion de compte à distance (via une smartphone par exemple)
$from = array("country" => "FR", "zipcode" => "44000", "type" => "particulier");
$to = array("country" => "FR", "zipcode" => $_GET["cp"], "type" => "particulier",);
 
// faire la cotation
$quotInfo = array("collecte_date" => "2011-04-26", "delay" => "aucun",  "content_code" => 50113);
$cotCl = new Env_Quotation(array("user" => "bbc", "pass" => "bbc", "key" => "bbc"));
$cotCl->setPerson("shipper", $from);
$cotCl->setPerson("recipient", $to);
$cotCl->setType("package", array("weight" => 2, "length" => 30, "width" => 44, "height" => 44));
$cotCl->getQuotation($quotInfo);
if(!$cotCl->curlError) {
  $cotCl->getOffers(true); 
}
else {
  echo "<b>Une erreur pendant l'envoi de la requête </b> : ".$cotCl->curlErrorText;
  die();
}
  
foreach($cotCl->offers as $o => $offre) {
?>
<tr id="ope-<?php echo $o;?>-tr">
<td><input type="radio" name="ope" id="ope-<?php echo $o;?>" value="<?php echo $offre["operator"]["code"];?>" class="chkbox selectOpe" /> <label for="ope-<?php echo $o;?>">choisir cette offre</label></td>
<td><img src="<?php echo $offre["operator"]["logo"];?>" alt="" /></td>
<td><?php  echo implode("<br /> - ", $offre["characteristics"]);?></td>
<td class="price"><?php echo $offre['price']['tax-exclusive'];?>€ <input type="hidden" name="ope-<?php echo $o;?>-price" id="ope-<?php echo $o;?>-price" value="<?php echo $offre['price']['tax-exclusive'];?>" /></td>
<td>
<?php if(count($offre['mandatory']['info_10.pointrelais']) > 0) { ?>
<a href="/api/demo/get_pr.php?qui=exp" rel="#popupPrShip" class="arrow smaller selectPr">sélectionnez le point de proximité d'expédition</a>
<span id="pr-exp-<?php echo $o;?>" class="hidden"><?php echo $offre['mandatory']['info_210.pointrelais']['type'];?></span>
<?php } ?><br />
<?php if(count($offre['mandatory']['info_210.pointrelais']) > 0) { ?>
<a href="/api/demo/get_pr.php?qui=dest" rel="#popupPrRec" class="arrow smaller selectPr">sélectionnez le point de proximité de livraison</a>
<span id="pr-dest-<?php echo $o;?>" class="hidden"><?php echo $offre['mandatory']['info_210.pointrelais']['type'];?></span>
<?php } ?>
</td>
</tr>
<?php
}
?> 