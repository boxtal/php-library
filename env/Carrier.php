<?php
/** 
 * EnvoiMoinsCher API carrier class.
 * 
 * It can be used to load informations about carriers and theirs services. 
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_Carrier extends Env_WebService {

  /** 
   *  Contains carriers array
	 *  Organisation :
	 *	$carriers[code] 	=> array(
	 *  	['label'] 				=> data
	 *  	['code'] 					=> data
	 *  	['logo'] 					=> data
	 *  	['logo_modules'] 	=> data
	 *  	['description'] 	=> data
	 *  	['address'] 			=> data
	 *  	['url'] 					=> data
	 *  	['tracking']			=> data
	 *		['tel'] 					=> data
	 *		['cgv'] 					=> data
	 *  )
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
   *  Function executes carrier request and prepares the $listPoints array.
   *  @access private
   *  @return void
   */
  private function doCarrierRequest() {
    $source = $this->doRequest();
		
		/* Uncomment if ou want to display the XML content */
		//echo '<textarea>'.$source.'</textarea>';
		
		/* We make sure there is an XML answer and try to parse it */
    if($source !== false) {
      parent::parseResponse($source);
	  	if(count($this->respErrorsList) == 0) {
				
				/* The XML file is loaded, we now gather the datas */
				$carriers = $this->xpath->query("/operators/operator");
				foreach($carriers as $c => $carrier) {
					$result = $this->parseCarrierNode($carrier);
					/* We usr the 'code' data as index (maybe using the $c index is better) */
					$code = $this->xpath->query('./code',$carrier)->item(0)->nodeValue;
					$this->carriers[$code] = array(
						'label' => $this->xpath->query('./label',$carrier)->item(0)->nodeValue,
						'code' => $this->xpath->query('./code',$carrier)->item(0)->nodeValue, 
						'logo' => $this->xpath->query('./logo',$carrier)->item(0)->nodeValue,
						'logo_modules' => $this->xpath->query('./logo_modules',$carrier)->item(0)->nodeValue,
						'description' => $this->xpath->query('./description',$carrier)->item(0)->nodeValue,
						'address' => $this->xpath->query('./address',$carrier)->item(0)->nodeValue,
						'url' => $this->xpath->query('./url',$carrier)->item(0)->nodeValue,
						'tracking' => $this->xpath->query('./tracking',$carrier)->item(0)->nodeValue,
						'tel' => $this->xpath->query('./telephone',$carrier)->item(0)->nodeValue,
						'cgv' => $this->xpath->query('./cgv',$carrier)->item(0)->nodeValue);
				}
			}
    }
  }

}
?>