<?php
/** 
 * EnvoiMoinsCher API quotation class.
 * 
 * The class is used to obtain a quotation and, if possibly, order it.
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_Quotation extends Env_WebService {

  /** 
   *  Public variable represents offers array. 
   *  @access public
   *  @var array
   */
  public $offers = array();

  /** 
   *  Public array containing order informations like order number, order date...
   *  @access public
   *  @var array
   */
  public $order = array();

  /** 
   *  Protected variable with pallet dimensions accepted by EnvoiMoinsCher.com. The dimensions are given
   *  in format "length cm x width cm". They are sorted from the longest to the shortest.
   *  <br />To pass a correct pallet values, use the $palletDimss' key in your "pallet" parameter.
   *  For exemple : 
   *  $quotInfo = array("collecte_date" => "2015-04-29", "delay" => "aucun",  "content_code" => 10120,
   *  <b>"pallet" => 130110</b>);
   *  <br />$this->makeOrder($quotInfo, true); 
   *  @access protected
   *  @var array
   */
  protected $palletDims = array(130110 => "130x110", 122102 => "122x102", 120120 => "120x120", 120100 => "120x100",
                           12080 => "120x80" , 114114 => "114x114", 11476 => "114x76", 110110 => "110x110",
                           107107 => "107x107", 8060 => "80x60"
                          );

  /** 
   *  Protected variable with shipment reasons. It is used to generate proforma invoice.
   *  Exemple of utilisation : 
   *  $quotInfo = array("collecte_date" => "2015-04-29", "delay" => "aucun",  "content_code" => 10120,
   *  "operator" => "UPSE", <b>"reason" => "repair"</b>);
   *  <br />$this->makeOrder($quotInfo, true);
   *  @access protected
   *  @var array
   */
  protected $shipReasons = array("sale" => "sale", "repair" => "repr", "return" => "rtrn", "gift" => "gift",
                           "sample" => "smpl" , "personnal" => "prsu", "document" => "icdt", "other" => "othr");

  /** 
   *  Public setter used to pass proforma parameters into the api request.
   *  <br />You must pass a multidimentional array, even for one line.
   *  <br /><b>The array keys must start with 1, not with 0.</b>
   *  <br />Exemple : 
   *  $this->setProforma(array(1 => array("description_en" => "english description for this item",
   *  "description_fr" => "la description française pour ce produit", "origine" => "FR", 
   *  "number" => 2, "value" => 500)));
   *  <br />The sense of keys in the proforma array : 
   *  <ul>
   *  <li><i>description_en</i> => description of your item in English</li>
   *  <li><i>description_fr</i> => description of your item in French</li>
   *  <li><i>origine</i> => origin of your item (you can put EEE four every product which comes 
   *  from EEA (European Economic Area))</li>
   *  <li><i>number</i> => quantity of items which you send</li>
   *  <li><i>value</i> => unitary value of <b>one</b> item </li>
   *  </ul>
   *  @access public
   *  @param array $data Array with proforma informations.
   *  @return void
   */
  public function setProforma($data) {
    foreach($data as $key => $value) {
      foreach($value as $lineKey => $lineValue) {
        $this->param["proforma_".$key.".".$lineKey] = $lineValue;
      }
    }
  }

  /** 
   *  Function which sets informations about package. 
   *  <br />Please note that if you send the pallet cotation, you can't indicate the dimensions like for
   *  other objects. In this case, you must pass the key from $palletDims protected variable. If the key
   *  is not passed, the request will return an empty result. 
   *  @access public
   *  @param string $type Type : pli, colis, encombrant, palette.
   *  @param array $data Array with package informations : weight, length, width and height.
   *  @return void
   */
  public function setType($type, $dimensions) {
    foreach($dimensions as $d => $data) {
      $this->param[$type."_$d.poids"] = $data["poids"];
      if($type == "palette") {
        $palletDim = explode("x", $this->palletDims[$data['palletDims']]);
        $data[$type."_$d.longueur"] = (int)$palletDim[0];
        $data[$type."_$d.largeur"] = (int)$palletDim[1];
      }
      $this->param[$type."_$d.longueur"] = $data["longueur"];
      $this->param[$type."_$d.largeur"] = $data["largeur"];
      if($type != "pli") {
        $this->param[$type."_$d.hauteur"] = $data["hauteur"];
      }
    }
  }

  /** 
   *  Public function which sets shipper and recipient objects.
   *  @access public
   *  @param string $type Person type (shipper or recipient).
   *  @param array $data Array with person informations.
   *  @return void
   */
  public function setPerson($type, $data) {
    foreach($data as $key => $value) {
      $this->param["$type.$key"] = $value;
    }
  }

  /** 
   *  Public function which receives the quotation. 
   *  @access public
   *  @param array $data Array with quotation demand informations (date, type, delay and insurance value).
   *  @return true if request was executed correctly, false if not
   */
  public function getQuotation($quotInfo) {
    $this->param = array_merge($this->param, $quotInfo);
    $this->setGetParams(array());
    $this->setOptions(array("action" => "/api/v1/cotation"));
    return $this->doSimpleRequest();
  }

  /** 
   *  Function which gets quotation details.
   *  @access private
   *  @return false if server response isn't correct; true if it is
   */
  private function doSimpleRequest() {
    $source = parent::doRequest();
    if($source !== false) {
      parent::parseResponse($source);
      return true;
    }
    return false;
  }

  /** 
   *  Public getter to parse and prepare offers array.
   *  @access public
   *  @param bool $onlyCom If true, we have to get only offers in the "order" mode.
   *  @return void
   */
  public function getOffers($onlyCom = false) {
    $offers = $this->xpath->evaluate("/cotation/shipment/offer");
    $of = 0;
    $l = 0;
    foreach($offers as $o => $offer) {
      $offerMode = $this->xpath->evaluate(".//offer/mode")->item($o)->nodeValue;
      if(!$onlyCom || ($onlyCom && $offerMode == "COM")) {
        $node = $o + 1; // node number (from 1)
        
        // mandatory informations - you must fill it up when you want to order this offer
        $informations = $this->xpath->evaluate("/cotation/shipment/offer[$node]/mandatory_informations/parameter");
	    $mandInfos = array();
        foreach($informations as $m => $mandatory) {
          $arrKey = $mandatory->getElementsByTagName("code")->item(0)->nodeValue;
          $mandInfos[$arrKey] = array();
          foreach($mandatory->childNodes as $mc => $mandatoryChild) {
            $mandInfos[$arrKey][$mandatoryChild->nodeName] = trim($mandatoryChild->nodeValue);
            if($mandatoryChild->nodeName == "type") {
              foreach($mandatoryChild->childNodes as $node) { 
	            if($node->nodeName == "enum") {
                  $mandInfos[$arrKey][$mandatoryChild->nodeName] = "enum";
                  $mandInfos[$arrKey]['array'] = array();
                  foreach($node->childNodes as $child) {
                    if(trim($child->nodeValue) != "") {
                      $mandInfos[$arrKey]['array'][] = $child->nodeValue;
                    }
                  }
                }
                else {
                  $mandInfos[$arrKey][$mandatoryChild->nodeName] = $node->nodeName;
                }
              }
            }
          }
          unset($mandInfos[$arrKey]["#text"]);
        }
        // characteristics generation
        $charactDetail = $this->xpath->evaluate("/cotation/shipment/offer/characteristics")->item($o)->childNodes;
        $charactArray = array();
        foreach($charactDetail as $c => $char) {
// TODO : enlever cette validation après avoir détecté pourquoi il multiplie le nombre des nodes par 2
          if(trim($char->nodeValue) != "") {
            $charactArray[$c] = $char->nodeValue;
          }
        }
        $alert = "";
        if(!empty($this->xpath->evaluate(".//offer/alert")->item($o)->nodeValue)) {
          $alert = $this->xpath->evaluate(".//offer/alert")->item($o)->nodeValue;
        }
        $this->offers[$of] = array(
          "mode" => $offerMode,
          "url" => $this->xpath->evaluate(".//offer/url")->item($o)->nodeValue,
          "operator" => array(
            "code" => $this->xpath->evaluate(".//offer/operator/code")->item($o)->nodeValue,
            "label" => $this->xpath->evaluate(".//offer/operator/label")->item($o)->nodeValue,
            "logo" => $this->xpath->evaluate(".//offer/operator/logo")->item($o)->nodeValue 
          ),
          "service" => array(
            "code" => $this->xpath->evaluate(".//service/code")->item($o)->nodeValue,
            "label" => $this->xpath->evaluate(".//service/label")->item($o)->nodeValue
          ), 
          "price" => array(
            "currency" => $this->xpath->evaluate(".//offer/price/currency")->item($o)->nodeValue,
            "tax-exclusive" => $this->xpath->evaluate(".//offer/price/tax-exclusive")->item($o)->nodeValue,
            "tax-inclusive" => $this->xpath->evaluate(".//offer/price/tax-inclusive")->item($o)->nodeValue
          ), 
          "collection" => array(
            "type" => $this->xpath->evaluate(".//collection/type/code")->item($o)->nodeValue,
            "date" => $this->xpath->evaluate(".//collection/date")->item($o)->nodeValue,
            "label" => $this->xpath->evaluate(".//collection/type/label")->item($o)->nodeValue
          ),
          "delivery" => array(
            "type" => $this->xpath->evaluate(".//delivery/type/code")->item($o)->nodeValue,
            "date" => $this->xpath->evaluate(".//delivery/date")->item($o)->nodeValue,
            "label" => $this->xpath->evaluate(".//delivery/type/label")->item($o)->nodeValue
          ),
          "characteristics" => $charactArray,
          "alert" => $alert,
          "mandatory" => $mandInfos
        );
        $of++;
      }
    }
  }

  /** 
   *  Get order informations about collection, delivery, offer, price, service, operator, alerts
   *  and characteristics.
   *  @access private
   *  @return void
   */
  private function getOrderInfos() {
    $this->order["url"] = $this->xpath->evaluate(".//url")->item(0)->nodeValue;
    $this->order["mode"] = $this->xpath->evaluate(".//mode")->item(0)->nodeValue;
    $this->order["offer"]["operator"]["code"] = $this->xpath->evaluate(".//operator/code")->item(0)->nodeValue;
    $this->order["offer"]["operator"]["label"] = $this->xpath->evaluate(".//operator/label")->item(0)->nodeValue;
    $this->order["offer"]["operator"]["logo"] = $this->xpath->evaluate(".//operator/logo")->item(0)->nodeValue;
    $this->order["service"]["code"] = $this->xpath->evaluate(".//service/code")->item(0)->nodeValue;
    $this->order["service"]["label"] = $this->xpath->evaluate(".//service/label")->item(0)->nodeValue;
    $this->order["price"]["currency"] = $this->xpath->evaluate(".//service/code")->item(0)->nodeValue;
    $this->order["price"]["tax-exclusive"] = $this->xpath->evaluate(".//price/tax-exclusive")->item(0)->nodeValue;
    $this->order["price"]["tax-inclusive"] = $this->xpath->evaluate(".//price/tax-inclusive")->item(0)->nodeValue;
    $this->order["collection"]["code"] = $this->xpath->evaluate(".//collection/type/code")->item(0)->nodeValue;
    $this->order["collection"]["type_label"] = $this->xpath->evaluate(".//collection/type/label")->item(0)->nodeValue;
    $this->order["collection"]["date"] = $this->xpath->evaluate(".//collection/date")->item(0)->nodeValue;
    $this->order["collection"]["time"] = $this->xpath->evaluate(".//collection/time")->item(0)->nodeValue;
    $this->order["collection"]["label"] = $this->xpath->evaluate(".//collection/label")->item(0)->nodeValue;
    $this->order["delivery"]["code"] = $this->xpath->evaluate(".//delivery/type/code")->item(0)->nodeValue;
    $this->order["delivery"]["type_label"] = $this->xpath->evaluate(".//delivery/type/label")->item(0)->nodeValue;
    $this->order["delivery"]["date"] = $this->xpath->evaluate(".//delivery/date")->item(0)->nodeValue;
    $this->order["delivery"]["time"] = $this->xpath->evaluate(".//delivery/time")->item(0)->nodeValue;
    $this->order["delivery"]["label"] = $this->xpath->evaluate(".//delivery/label")->item(0)->nodeValue;
    $this->order["proforma"] = $this->xpath->evaluate(".//proforma")->item(0)->nodeValue;
    $this->order["alerts"] = array(); 
    $alertsNodes = $this->xpath->evaluate(".//alert");
    foreach($alertsNodes as $a => $alert) {
      $this->order["alerts"][$a] = $alert->nodeValue;  
    }
    $charNodes = $this->xpath->evaluate(".//characteristics/label");
    foreach($charNodes as $c => $char) {
      $this->order["chars"][$c] = $char->nodeValue;
    }
    $this->order["labels"] = array();
    $labelNodes = $this->xpath->evaluate(".//labels/label");
    foreach($labelNodes as $l => $label) {
      $this->order["labels"][$l] = trim($label->nodeValue);  
    }
  }

  /** 
   *  Public function which sends order request.
   *  <br />If you don't want to pass insurance parameter, you have to make insurance to false
   *  in your parameters array ($quotInfo). It checks also if you pass insurance parameter 
   *  which is obligatory to order a transport service.
   *  <br />The response should contains a order number composed by 10 numbers, 4 letters, 4
   *  number and 2 letters. We use this rule to check if the order was correctly executed 
   *  by API server.
   *  @access public
   *  @param array $data Array with order informations (date, type, delay).
   *  @param boolean $getInfo Precise if we want to get more informations about order.
   *  @return boolean True if order was passed successfully; false if an error occured. 
   */
  public function makeOrder($quotInfo, $getInfo = false) {
    $this->quotInfo = $quotInfo;
    $this->getInfo = $getInfo;
    if($quotInfo["reason"]) {
      $quotInfo["envoi.raison"] = $this->shipReasons[$quotInfo["reason"]];
      unset($quotInfo["reason"]);
    }
    if($quotInfo["assurance.selected"] == "") {
      $quotInfo["assurance.selected"] = false;
    }
    $this->param = array_merge($this->param, $quotInfo);
    $this->setOptions(array("action" => "/api/v1/order"));
    $this->setPost();
    if($this->doSimpleRequest() && !$this->respError) {
      // check the order reference
	  $nodes = $this->xpath->evaluate("/order/shipment");
      $reference = $nodes->item(0)->getElementsByTagName("reference")->item(0)->nodeValue;
	  if(preg_match("/^[0-9]{10}[A-Z]{4}[0-9]{4}[A-Z]{2}$/", $reference)) {
        $this->order["ref"] = $reference;
        $this->order["date"] = date("Y-m-d H:i:s");
        if($getInfo) {
          $this->getOrderInfos();
        }
        return true;
      }
      return false;
    }
    else {
      return false;
    }
  }


  /** 
   *  Public getter of shippment reasons
   *  @access public
   *  @param array $translations Array with reasons' translations. You must translate by $this->shipReasons values, 
   *  not the keys.
   *  @return array Array with shippment reasons, may by used to pro forma generation. 
   */
  public function getReasons($translations) {
    $reasons = array();
    if(count($translations) == 0)
    {
      $translations = $this->shipReasons;
    }
    foreach($this->shipReasons as $r => $reason)
    {
      $reasons[$reason] = $translations[$r];
    }
    return $reasons;
  }


  /** 
   *  Method which allowes you to make double order (the same order in two directions : from shipper 
   *  to recipient and from recipient to shipper). It can be used by some stores for send a test product
   *  to customer and receive it back if the customer isn't satisfied. 
   *  @return boolean True if second order was passed successfully; false if an error occured. 
   */
  public function makeDoubleOrder($quotInfo = array(), $getInfo = false) {
    if(count($quotInfo) == 0) {
      $quotInfo = $this->quotInfo;
    }
    else {
      $quotInfo = $this->setNewQuotInfo($quotInfo);
    }
    $this->switchPeople();
    $this->makeOrder($quotInfo, $getInfo);
  }

  /** 
   *  Person switcher; it switchs shipper to recipient and recipient to shipper.  
   *  @return void
   */
  private function switchPeople() {
    $localParams = $this->param;
    $old = array("expediteur", "destinataire", "tmp_exp", "tmp_dest");
    $new = array("tmp_exp", "tmp_dest", "destinataire", "expediteur");
    foreach($localParams as $key => $value) {
      $this->param[str_replace($old, $new, $key)] = $value;
    }
  }

  /** 
   *  Setter for new request parameters. If a new parameter is defined, it overriddes the old one (for exemple new service,
   *  new hour disponibility).
   *  @return array Array containing new quotation informations.
   */
  private function setNewQuotInfo($quotInfo) {
    foreach((array)$this->quotInfo as $q => $info) {
      if(array_key_exists($q, $quotInfo)) {
        $this->quotInfo[$q] = $quotInfo[$q];
      }
    }
    foreach($quotInfo as $q => $info) {
      if(!array_key_exists($q, (array)$this->quotInfo)) {
        $this->quotInfo[$q] = $quotInfo[$q];
      }
    }
    return $this->quotInfo;
  }

  /** 
   *  Method which removes old quotation parameters.
   *  @return void
   */
  public function unsetParams($quotInfo) {
    foreach($quotInfo as $info) {
      unset($this->quotInfo[$info]);
      unset($this->param[$info]);
    }
  }

}
?>