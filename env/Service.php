<?php
/** 
 * EnvoiMoinsCher API carrier's services class.
 * 
 * It can be used to load informations about carrier's services.
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
    $this->setOptions(array('action' => '/api/v1/services',
		));
    $this->doServicesRequest();
  }

  /**
   *  Function executes services request and prepares the $carriers array.
   *  @access private
   *  @return void
   */
  private function doServicesRequest() {
    $source = $this->doRequest();
    if($source !== false) {
      parent::parseResponse($source);
      $carriers = $this->xpath->query('/operators/operator');
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
    $this->setOptions(array("action" => '/api/v1/carrier/'.$code.'/services'));
    $this->doServicesRequest();
  }

  /** 
   *  Parser for service node list.
   *  @access private
   *  @param $c : Node index.
   *  @return array Array with all available informations about the service
	 *  Organisation :
	 *	$return[code] 			=> array(
	 *  	['code'] 						=> data
	 *  	['label'] 					=> data
	 *  	['mode'] 						=> data
	 *  	['alert'] 					=> data
	 *  	['collection'] 			=> data
	 *  	['delivery'] 				=> data
	 *  	['is_pluggable'] 		=> data
	 *  	['options'][code]		=> array(
	 *			['name'] 						=> data
	 *		)
	 *  	['exclusions'][id]	=> array(
	 *			['label'] 				=> data
	 *		)
	 *  	['apiOptions'][option][option2]	=> data
	 *  )
   */
  private function parseServicesNode($c) {
    $result = array();
    $services = $this->xpath->query('/operators/operator['.$c.']/services/service');
    foreach($services as $se => $service) {
      $s = $se + 1;
      $code = $this->xpath->query('./code',$service)->item(0)->nodeValue;
      $result[$code] = array(
				"code" => $code,
        "label" => $this->xpath->query('./label',$service)->item(0)->nodeValue,
        "mode" => $this->xpath->query('./mode',$service)->item(0)->nodeValue,
        "alert" => $this->xpath->query('./alert',$service)->item(0)->nodeValue,
        "collection" => $this->xpath->query('./collection_type',$service)->item(0)->nodeValue,
        "delivery" => $this->xpath->query('./delivery_type',$service)->item(0)->nodeValue,
        "is_pluggable" => ($this->xpath->query('./plug_available',$service)->item(0)->nodeValue == "true" ? true : false)
      );
      $options = array();
      $exclusions = array();
      $apiOptions = array();
      foreach($this->xpath->evaluate('./options/option',$service) as $o => $option) {
        $options[$this->xpath->evaluate('./code',$option)->item(0)->nodeValue] = $this->xpath->evaluate('./name',$option)->item(0)->nodeValue;
      }
      $result[$code]['options'] = $options;
      foreach($this->xpath->evaluate('./excluded_contents/contenu',$service) as $e => $exclusion) {
        $exclusions[$this->xpath->evaluate('./id',$exclusion)->item(0)->nodeValue] = $this->xpath->evaluate("./label",$exclusion)->item(0)->nodeValue;
      }
      $result[$code]['exclusions'] = $exclusions;
      foreach($this->xpath->evaluate('./api_options',$service) as $o => $option) {
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
      $result[$code]['apiOptions'] = $apiOptions;
    }
    return $result;
  }

}
?>