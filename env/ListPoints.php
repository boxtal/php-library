<?php
/** 
 * EnvoiMoinsCher API order status class.
 * 
 * It can be used to get informations about passed order (label availability, carrier reference). 
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_ListPoints extends Env_WebService {

  /** 
   *  Contains order informations.
   *  @access public
   *  @var array
   */
  public $listPoints = array();

  /**
   *  Function loads all categories.
   *  @access public
   *  @return void
   */
  public function getListPoints($ope, $infos) {
		$this->param = $infos;
		$this->setGetParams(array());
    $this->setOptions(array("action" => "/api/v1/$ope/listpoints"));
    $this->doListRequest();
  }
  
  /** 
   *  Function executes categories request and prepares the $categories array.
   *  @access private
   *  @return void
   */
  private function doListRequest() {
    $source = parent::doRequest();
    if($source !== false) {
      parent::parseResponse($source);
	  	if(count($this->respErrorsList) == 0) {
				$points = $this->xpath->query("/point");
      }
    }
  }

}

?>
