<?php
/** 
 * EnvoiMoinsCher API carrier class.
 * 
 * It can be used to load carriers and theirs services. 
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_Carrier extends Env_WebService {

  /** 
   *  Carriers array
   *  @access public
   *  @var array
   */
  public $carriers = array();

  /** 
   *  Function loads all carriers.
   *  @access public
   *  @return void
   */
  public function getCarriers() { 
    $this->setOptions(array("action" => "/api/v1/carriers",
	));
    $this->doCarrierRequest();
  }

  /** 
   *  Parser for get_carriers resource.
   *  @access private
   *  @return void
   */
  private function doCarrierRequest() {
    $source = $this->doRequest();
    if($source !== false) {
      parent::parseResponse($source);
      $carriers = $this->xpath->query("/operators/operator");
      foreach($carriers as $c => $carrier) {
        $result = $this->parseCarrierNode($c);
        $this->carriers[$result["code"]] = $result;
      }
    }
  }

  /** 
   *  Parser for get_carriers resource.
   *  @access private
   *  @param int $carrier Parsed carrier's index.
   *  @return array Array with carrier's data.
   */
  public function parseCarrierNode($carrier) {
    return array("label" => $this->xpath->evaluate("/operators/operator[$carrier]/label")->item(0)->nodeValue,
    "code" => $this->xpath->evaluate("/operators/operator[$carrier]/code")->item(0)->nodeValue, 
    "logo" => $this->xpath->evaluate("/operators/operator[$carrier]/logo")->item(0)->nodeValue,
    "logo_modules" => $this->xpath->evaluate("/operators/operator[$carrier]/logo_modules")->item(0)->nodeValue,
    "description" => $this->xpath->evaluate("/operators/operator[$carrier]/description")->item(0)->nodeValue,
    "address" => $this->xpath->evaluate("/operators/operator[$carrier]/address")->item(0)->nodeValue,
    "url" => $this->xpath->evaluate("/operators/operator[$carrier]/url")->item(0)->nodeValue,
    "tracking" => $this->xpath->evaluate("/operators/operator[$carrier]/tracking")->item(0)->nodeValue,
    "tel" => $this->xpath->evaluate("/operators/operator[$carrier]/telephone")->item(0)->nodeValue,
    "cgv" => $this->xpath->evaluate("/operators/operator[$carrier]/cgv")->item(0)->nodeValue);
  }

}
?>