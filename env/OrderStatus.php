<?php
/** 
 * EnvoiMoinsCher API order status class.
 * 
 * It can be used to get informations about passed order (label availability, carrier reference). 
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_OrderStatus extends Env_WebService {

  /** 
   *  Contains order informations.
   *  @access public
   *  @var array
   */
  public $orderInfo = array("emcRef" => "", "state" => "", "opeRef" => "", "labelAvailable" => false);

  /**
   *  Function loads all categories.
   *  @access public
   *  @return void
   */
  public function getOrderInformations($reference) { 
    $this->setOptions(array("action" => "/api/v1/order_status/$reference/informations",
	));
    $this->doStatusRequest();
  }
  
  /** 
   *  Function executes categories request and prepares the $categories array.
   *  @access private
   *  @return void
   */
  private function doStatusRequest() {
    $source = parent::doRequest();
    if($source !== false) {
      parent::parseResponse($source);
      $this->orderInfo = array("emcRef" => $this->xpath->evaluate("/order/emc_reference")->item(0)->nodeValue, 
        "state" => $this->xpath->evaluate("/order/state")->item(0)->nodeValue, 
        "opeRef" => $this->xpath->evaluate("/order/carrier_reference")->item(0)->nodeValue, 
        "labelAvailable" => (bool)$this->xpath->evaluate("/order/label_available")->item(0)->nodeValue,
        "labelUrl" => $this->xpath->evaluate("/order/label_url")->item(0)->nodeValue
      );
    }
  }

}

?>