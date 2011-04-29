<?php
/** 
 * EnvoiMoinsCher API quotation class.
 * 
 * The class is used to obtain a quotation and, if possibly, order it.
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_Quotation extends Env_WebService {

  /** Public variable represents offers array. 
   *  @access public
   *  @var array
   */
  public $offers = array();

  /** Public array containing command informations like order number, order date
   *  @access public
   *  @var array
   */
  public $command = array();

  /** Protected variable with pallet dimensions accepted by EnvoiMoinsCher.com. The dimensions are givent
   *  by format "length cm x width cm". They are sorted by from the longest to the shortest.
   *  @access protected
   *  @var array
   */
  protected $palletDims = array(130110 => "130x110", 122102 => "122x102", 120120 => "120x120", 120100 => "120x100",
                           12080 => "120x80" , 114114 => "114x114", 11476 => "114x76", 110110 => "110x110",
                           107107 => "107x107", 8060 => "80x60"
                          );

  /** Protected variable with shipment reasons. It is used to generate proforma invoice.
   *  @access protected
   *  @var array
   */
  protected $shipReasons = array("sale" => "sale", "repair" => "repr", "return" => "rtrn", "gift" => "gift",
                           "sample" => "smpl" , "personnal" => "prsu", "document" => "icdt", "other" => "othr");

  /** Public setter used to pass proforma parameters into the api request.
   *  You must pass a multidimentional array. Later, we will accept to order more than one
   *  parcel.
   *  @access public
   *  @param array $data Array with invoice informations.
   *  @return void
   */
  public function setProforma($data) {
    foreach($data as $key => $value) {
      foreach($value as $lineKey => $lineValue) {
        $this->param["proforma_".$key.".".$lineKey] = $lineValue;
      }
    }
  }

  /** Function which sets informations about package. 
   *  Please note that if you send the pallet cotation, you can't indicate the dimensions like for
   *  other objects. In this case, you must pass the key from $palletDims protected variable. If the key
   *  is not passed, the request will return an empty result. 
   *  @access public
   *  @param string $type Type : letter, package, bulky or pallet.
   *  @param array $data Array with package informations : weight, length, width and height.
   *  @return void
   */
  public function setType($type, $data) {
    $this->param["$type.weight"] = $data["weight"];
    if($type == "pallet") {
      $palletDim = explode("x", $this->palletDims[$data['pallet']]);
      $data["length"] = (int)$palletDim[0];
      $data["width"] = (int)$palletDim[1];
    }
    $this->param["$type.length"] = $data["length"];
    $this->param["$type.width"] = $data["width"];
    if($type != "letter") {
      $this->param["$type.height"] = $data["height"];
    }
  }

  /** Public function which sets shipper and recipient objects.
   *  @access public
   *  @param string $type Person type (shipeper or recipient).
   *  @param array $data Array with person informations.
   *  @return void
   */
  public function setPerson($type, $data) {
    foreach($data as $key => $value) {
      $this->param["$type.$key"] = $value;
    }
  }

  /** Public function which receives the quotation. 
   *  @access public
   *  @param array $data Array with quotation demand informations (date, type, delay and insurance value).
   *  @return void
   */
  public function getQuotation($quotInfo) {
    $this->param = array_merge($this->param, $quotInfo);
    $this->setGetParams();
    $this->setOptions(array("action" => "/api/v1/cotation"));
    return $this->doSimpleRequest();
  }

  /** Function which gets quotation details.
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

  /** Public getter to parse and prepare offers array.
   *  @access public
   *  @param bool $onlyCom If true, we have to get only offers in the "command" mode.
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
        
        // mandatory informations - you must inform it to be able to order an offer
        $informations = $this->xpath->evaluate("/cotation/shipment/offer[$node]/mandatory_informations/parameter");
	    $mandInfos = array();
        foreach($informations as $m => $mandatory) {
          $arrKey = $mandatory->getElementsByTagName("code")->item(0)->nodeValue;
          $mandInfos[$arrKey] = array();
          foreach($mandatory->childNodes as $mc => $mandatoryChild) {
            $mandInfos[$arrKey][$mandatoryChild->nodeName] = trim($mandatoryChild->nodeValue);
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
// préparation des alertes (TODO : modif dans le draft où l'on indique alerte au lieu d'alert)
        $alerts = array(); 
        $alertsNode = $this->xpath->query("/cotation/shipment/offer[$node]/alert");
        foreach($alertsNode as $a => $alert) {
          $alerts[$a] = $alert->nodeValue;  
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
            "type" => $this->xpath->evaluate(".//collection/type")->item($o)->nodeValue,
            "date" => $this->xpath->evaluate(".//collection/date")->item($o)->nodeValue,
            "label" => $this->xpath->evaluate(".//collection/label")->item($o)->nodeValue
          ),
          "delivery" => array(
            "type" => $this->xpath->evaluate(".//delivery/type")->item($o)->nodeValue,
            "date" => $this->xpath->evaluate(".//delivery/date")->item($o)->nodeValue,
            "label" => $this->xpath->evaluate(".//delivery/label")->item($o)->nodeValue
          ),
          "characteristics" => $charactArray,
          "alerts" => $alerts,
          "mandatory" => $mandInfos
        );
        $of++;
      }
    }
  }

  /** Get order informations about collection, delivery, offer, price, service, operator, alerts
   *  and characteristics.
   *  @access private
   *  @return void
   */
  private function getOrderInfos() {
    $this->command["url"] = $this->xpath->evaluate(".//url")->item(0)->nodeValue;
    $this->command["mode"] = $this->xpath->evaluate(".//mode")->item(0)->nodeValue;
    $this->command["offer"]["operator"]["code"] = $this->xpath->evaluate(".//operator/code")->item(0)->nodeValue;
    $this->command["offer"]["operator"]["label"] = $this->xpath->evaluate(".//operator/label")->item(0)->nodeValue;
    $this->command["offer"]["operator"]["logo"] = $this->xpath->evaluate(".//operator/logo")->item(0)->nodeValue;
    $this->command["service"]["code"] = $this->xpath->evaluate(".//service/code")->item(0)->nodeValue;
    $this->command["service"]["label"] = $this->xpath->evaluate(".//service/label")->item(0)->nodeValue;
    $this->command["price"]["currency"] = $this->xpath->evaluate(".//service/code")->item(0)->nodeValue;
    $this->command["price"]["tax-exclusive"] = $this->xpath->evaluate(".//price/tax-exclusive")->item(0)->nodeValue;
    $this->command["price"]["tax-inclusive"] = $this->xpath->evaluate(".//price/tax-inclusive")->item(0)->nodeValue;
    $this->command["collection"]["code"] = $this->xpath->evaluate(".//collection/type/code")->item(0)->nodeValue;
    $this->command["collection"]["type_label"] = $this->xpath->evaluate(".//collection/type/label")->item(0)->nodeValue;
    $this->command["collection"]["date"] = $this->xpath->evaluate(".//collection/date")->item(0)->nodeValue;
    $this->command["collection"]["time"] = $this->xpath->evaluate(".//collection/time")->item(0)->nodeValue;
    $this->command["collection"]["label"] = $this->xpath->evaluate(".//collection/label")->item(0)->nodeValue;
    $this->command["delivery"]["code"] = $this->xpath->evaluate(".//delivery/type/code")->item(0)->nodeValue;
    $this->command["delivery"]["type_label"] = $this->xpath->evaluate(".//delivery/type/label")->item(0)->nodeValue;
    $this->command["delivery"]["date"] = $this->xpath->evaluate(".//delivery/date")->item(0)->nodeValue;
    $this->command["delivery"]["time"] = $this->xpath->evaluate(".//delivery/time")->item(0)->nodeValue;
    $this->command["delivery"]["label"] = $this->xpath->evaluate(".//delivery/label")->item(0)->nodeValue;
    $this->command["alerts"] = array(); 
    $alertsNodes = $this->xpath->evaluate(".//alert");
    foreach($alertsNodes as $a => $alert) {
      $this->command["alerts"][$a] = $alert->nodeValue;  
    }
    $charNodes = $this->xpath->evaluate(".//characteristics/label");
    foreach($charNodes as $c => $char) {
      $this->command["chars"][$c] = $char->nodeValue;
    }
  }

  /** Public function which sends order request.
   *  If you don't want to pass insurance parameter, you have to make insurance to false
   *  in your parameters array ($quotInfo). It checks also if you pass insurance parameter 
   *  which is obligatory to order a transport service.
   *  The response should contains a command number composed by 10 numbers, 4 letters, 4
   *  number and 2 letters. We use this rule to check if the order was correctly executed 
   *  by API server.
   *  @access public
   *  @param array $data Array with order informations (date, type, delay).
   *  @param boolean $getInfo Precise if we want to get more informations about order.
   *  @return boolean True if order was passed successfully; false if an error occured. 
   */
  public function makeOrder($quotInfo, $getInfo = false) {
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
    if($this->doSimpleRequest()) {
      // check the order reference
	  $nodes = $this->xpath->evaluate("/order/shipment");
      $reference = $nodes->item(0)->getElementsByTagName("reference")->item(0)->nodeValue;
	  if(preg_match("/^[0-9]{10}[A-Z]{4}[0-9]{4}[A-Z]{2}$/", $reference)) {
        $this->command["ref"] = $reference;
        $this->command["date"] = date("Y-m-d H:i:s");
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

}
?>