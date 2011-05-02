<?php  
ob_start();  
header('Content-Type: text/html; charset=utf-8');  
error_reporting(E_ERROR | E_WARNING | E_PARSE); 
require_once $_SERVER['DOCUMENT_ROOT'].'/librairie/utils/autoload.php'; 

foreach($_GET as $k => $get) {
  $_GET[$k] = mb_convert_encoding(urldecode($_GET[$k]), "UTF-8");
}

  // print_r($_GET);
// création du destinataire et de l'expéditeur - classe séparée car dans l'avenir on voudra 
// peut-être gérer les carnets d'adresses ou gestion de compte à distance (via une smartphone par exemple)
$from = array("country" => "FR", "zipcode" => "44000", "type" => "particulier",
"adresse" => "1, rue Racine");
$to = array("country" => "FR", "zipcode" => $_GET["cp"], "type" => "particulier",
"adresse" => $_GET["adresse"]);
// echo mb_convert_encoding(urldecode($_GET["adresse"]), "UTF-8");
// faire la cotation
$quotInfo = array("collecte" => "2011-05-03", "delay" => "aucun",  "content_code" => 50113);
if($_GET["ope"] != "" && $_GET["ope"] != "all") { 
  $quotInfo["operator"] = $_GET["ope"];
}
$cotCl = new Env_Quotation(array("user" => "bbc", "pass" => "bbc", "key" => "bbc"));
$cotCl->setPerson("shipper", $from);
$cotCl->setPerson("recipient", $to);
$cotCl->setType("package", array("weight" => 2, "length" => 30, "width" => 44, "height" => 44));
$cotCl->getQuotation($quotInfo);
 
if($cotCl->curlError) {     
  echo "<b>Une erreur pendant l'envoi de la requête </b> : ".$cotCl->curlErrorText;   
  die();     
}    
elseif($cotCl->respError) {   
  echo "La requête n'est pas valide : ";   
  foreach($cotCl->respErrorsList as $m => $message) { 
    echo "<br /><b>".$message['message']."</b>";    
  }  
  die();  
}
else {
  $cotCl->getOffers(true);
  foreach($cotCl->offers as $o => $offre) {
?>
<tr id="ope-<?php echo $o;?>-tr">
<td><input type="radio" name="ope" id="ope-<?php echo $o;?>" value="<?php echo $offre["operator"]["code"];?>" class="chkbox selectOpe" /> <label for="ope-<?php echo $o;?>">choisir cette offre</label></td>
<td><img src="http://www.envoimoinscher.com/images/logo_<?php echo strtolower($offre["operator"]["code"]);?>.gif" alt="" /></td>
<td><?php foreach($offre["characteristics"] as $c => $char) { 
  echo $char.'<br />';  
  unset($offre["characteristics"][$c]);  
  if($c == 3) { 
    break; 
  } 
}  
?>
<span id="char-<?php echo $o;?>" class="hidden"><?php echo implode("<br /> - ", $offre["characteristics"]); ?></span>
<p id="char-<?php echo $o;?>-show" class="arrow"><a href="#" rel="#char-<?php echo $o;?>" class="showMoreOpt">toutes les options</a></p>	
<p id="char-<?php echo $o;?>-hide" class="arrow hidden"><a href="#" rel="#char-<?php echo $o;?>" class="hideMoreOpt">moins d'options</a></p>	
</td>
<td class="price"><?php echo $offre['price']['tax-exclusive'];?>€ <input type="hidden" name="ope-<?php echo $o;?>-price" id="ope-<?php echo $o;?>-price" value="<?php echo $offre['price']['tax-exclusive'];?>" />
<input type="hidden" name="ope-<?php echo $o;?>-operator" id="ope-<?php echo $o;?>-operator" value="<?php echo $offre['operator']['label'];?>" />
<input type="hidden" name="ope-<?php echo $o;?>-service" id="ope-<?php echo $o;?>-service" value="<?php echo $offre['service']['label'];?>" />
<input type="hidden" name="ope-<?php echo $o;?>-infos" id="ope-<?php echo $o;?>-infos" value="<?php echo implode("<br /> - ", $offre["characteristics"]);?>" /></td>
<td>
<?php $time = time().rand(0,200000); if(count($offre['mandatory']['depot.pointrelais']) > 0) { ?>
  <?php $pr = explode(" ", $offre['mandatory']['depot.pointrelais']['type']);
    foreach($pr as $p => $point) {
      if(trim($point) != "") {
	    $poi[$p] = trim($point);
	  }
    } 
  ?>
<a href="/api/demo/demo_relais.php?type=exp&points=<?php echo implode(",", $poi);?>" rel="#pointsExp-<?php echo $time;?>" class="arrow smaller selectPr">sélectionnez le point de proximité de départ</a>
<div id="pointsExp-<?php echo $time;?>"></div>
<?php } ?><br />
<?php if(count($offre['mandatory']['retrait.pointrelais']) > 0) { ?>
  <?php $pr = explode(" ", $offre['mandatory']['retrait.pointrelais']['type']);
    foreach($pr as $p => $point) {
      if(trim($point) != "") {
	    $poi[$p] = trim($point);
	  }
    }  
  ?>
<a href="/api/demo/demo_relais.php?type=dest&points=<?php echo implode(",", $poi);?>" rel="#pointsLiv-<?php echo $time;?>" class="arrow smaller selectPr">sélectionnez le point de proximité d'arrivée</a>
<div id="pointsLiv-<?php echo $time;?>"></div>
<?php } ?>
</td>
</tr>
<?php
}

}
?> 