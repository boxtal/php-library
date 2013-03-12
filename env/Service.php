<?php
/** 
 * EnvoiMoinsCher API carrier's services class.
 * 
 * It can be used to load carrier's services.
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_Service extends Env_Carrier {

  /**
   *  Function loads services of all carriers.
   *  @access public
   *  @return void
   */
  public function getServices() { 
    $this->setOptions(array("action" => "/api/v1/services",
	));
    $this->doServicesRequest();
  }

  /**
   *  Parser for get_services resource.
   *  @access private
   *  @return void
   */
  private function doServicesRequest() {
    $source = $this->doRequest();
    if($source !== false) {
      parent::parseResponse($source);
      $carriers = $this->xpath->query("/operators/operator");
      foreach($carriers as $c => $carrier) {
        $index = $c + 1;
        $result = $this->parseCarrierNode($index);
        $this->carriers[$result["code"]] = $result;
        $this->carriers[$result["code"]]["services"] = $this->parseServicesNode($index);
      }
    }
  }

  /** 
   *  Getter for one carrier's code.
   *  @access private
   *  @return void
   */
  public function getServicesByCarrier($code) {
    if(isset($this->carriers[$code]["services"])) {
      return $this->carriers[$code]["services"];
    }
    $this->setOptions(array("action" => "/api/v1/carrier/$code/services"));
    $this->doServicesRequest();
  }

  /** 
   *  Parser for service node list.
   *  @access private
   *  @param int $c Node index.
   *  @return array Array with all available informations about the service
   */
  private function parseServicesNode($c) {
    $result = array();
    $services = $this->xpath->query("/operators/operator[$c]/services/service");
    foreach($services as $se => $service) {
      $s = $se + 1;
      $code = $this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/code")->item(0)->nodeValue;
      $result[$code] = array("code" => $code,
        "label" => $this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/label")->item(0)->nodeValue,
        "mode" => $this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/mode")->item(0)->nodeValue,
        "alert" => $this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/alert")->item(0)->nodeValue,
        "collection" => $this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/collection_type")->item(0)->nodeValue,
        "delivery" => $this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/delivery_type")->item(0)->nodeValue,
        "is_pluggable" => ($this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/plug_available")->item(0)->nodeValue == "true" ? true : false)
      );
      $options = array();
      $exclusions = array();
      $apiOptions = array();
      foreach($this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/options/option") as $o => $option) {
        $options[$this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/options/option/code")->item($o)->nodeValue] = $this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/options/option/name")->item($o)->nodeValue;
      }
      $result[$code]["options"] = $options;
      foreach($this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/excluded_contents/contenu") as $e => $exclusion) {
        $exclusions[$this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/excluded_contents/contenu/id")->item($e)->nodeValue] = $this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/excluded_contents/contenu/label")->item($e)->nodeValue;
      }
      $result[$code]["exclusions"] = $exclusions;
      foreach($this->xpath->evaluate("/operators/operator[$c]/services/service[$s]/api_options") as $o => $option) {
        for($i = 1; $i < $option->childNodes->length; $i++) {
// foreach($option->childNodes as $s => $sourceNode) {
          $apiNode = $option->childNodes->item($i);
                  $apiNodeChild = $apiNode->childNodes;
          $apiOptions[$apiNode->nodeName] = array();
          for($a = 1; $a < $apiNodeChild->length; $a++) {
            $apiOptions[$apiNode->nodeName][$apiNodeChild->item($a)->nodeName] = $apiNodeChild->item($a)->nodeValue;
            $a++;
          }
          $i++; 
        }
      }
      $result[$code]["apiOptions"] = $apiOptions;
    }
    return $result;
  }

}
?>