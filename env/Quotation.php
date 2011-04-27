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
    if(count($data) == 1) {
      foreach($data[0] as $key => $value) {
        $this->param["proforma.$key"] = $value;
      }
    }
    else {
      $input = array("0=", "1=", "2=", "3=");
      $output = array();
      foreach($data as $key => $value) {
        $l =0;
        foreach($value as $lineKey => $lineValue) {
          // problèmes avec l'envoi de la requête ====> $this->param["proforma_".$key.".".$lineKey] = $lineValue;
          $output[$l] = "proforma_".$key.".".$lineKey."=";
          $proforma[$l] = $lineValue; 
          $l++;
        }
        $proformaLine[$key] = str_replace($input, $output, http_build_query($proforma));
      }
      $this->proformaLine = "&".implode("&", $proformaLine);
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
    $this->doSimpleRequest($type);
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

  public function getQuotationInfos() {
// TODO : récupération de certaines informations de la cotation 
// TODO : voir si l'on récupère de la requête ou des options passées ?
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
   *  @return boolean True if order was passed successfully; false if an error occured. 
   */
  public function makeOrder($quotInfo) {
    if($quotInfo["reason"]) {
      $quotInfo["shipment.reason"] = $this->shipReasons[$quotInfo["reason"]];
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
	  $order = $this->xpath->evaluate("/order/shipment/reference"); 
	  foreach($order as $o => $or) { 
        $reference = $or->nodeValue;
        break;
      }
      if(preg_match("/^[0-9]{10}[A-Z]{4}[0-9]{4}[A-Z]{2}$/", $reference)) {
        $this->command["ref"] = $reference;
        $this->command["date"] = date("Y-m-d H:i:s");
        // TODO : get more parameters
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