<?php
/** 
 * EnvoiMoinsCher API list points class.
 * 
 * It can be used to load informations about parcel points (for pickup and dropoff)
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_ListPoints extends Env_WebService {

  /** 
   *  Contains Points informations
	 *  Organisation :
	 *	$listPoints[x] 	=> array(
	 *  	['code'] 				=> data
	 *  	['name'] 				=> data
	 *  	['address'] 		=> data
	 *  	['city'] 				=> data
	 *  	['zipcode'] 		=> data
	 *  	['country'] 		=> data
	 *  	['description'] => data
	 *  	['days'][x]			=> array(
	 *			['weekday'] 		=> data
	 *			['open_am'] 		=> data
	 *			['close_am']		=> data
	 *			['open_pm'] 		=> data
	 *			['close_pm']	 	=> data
	 *		)
	 *  )
   *  @access public
   *  @var array
   */
  public $listPoints = array();

  /**
   *  Function loads all points
	 *  @param $ope : folder ope
	 *  @param $infos : parameters for the request to the api
	 *           example : array('srv_code' => 'RelaisColis', 'pays' => 'FR', 'cp' => '75011', 'ville' => 'PARIS')
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
   *  Function executes points request and prepares the $listPoints array.
   *  @access private
   *  @return void
   */
  private function doListRequest() {
    $source = parent::doRequest();
		
		/* Uncomment if ou want to display the XML content */
		//echo '<textarea>'.$source.'</textarea>';
		
		/* We make sure there is an XML answer and try to parse it */
    if($source !== false) {
      parent::parseResponse($source);
	  	if(count($this->respErrorsList) == 0) {
				
				/* The XML file is loaded, we now gather the datas */
				$points = $this->xpath->query("/points/point");
				foreach($points as $pointIndex => $point){
					$pointInfo = array(
						'code' => $this->xpath->query('./code',$point)->item(0)->nodeValue,
						'name' => $this->xpath->query('./name',$point)->item(0)->nodeValue,
						'address' => $this->xpath->query('./address',$point)->item(0)->nodeValue,
						'city' => $this->xpath->query('./city',$point)->item(0)->nodeValue,
						'zipcode' => $this->xpath->query('./zipcode',$point)->item(0)->nodeValue,
						'country' => $this->xpath->query('./country',$point)->item(0)->nodeValue,
						'phone' => $this->xpath->query('./phone',$point)->item(0)->nodeValue,
						'description' => $this->xpath->query('./description',$point)->item(0)->nodeValue,
						'days' => array()
						);
					$days = $this->xpath->query('./schedule/day',$point);
					foreach($days as $dayIndex => $day){
						$pointInfo['days'][$dayIndex] = array(
							'weekday' => $this->xpath->query('./weekday',$day)->item(0)->nodeValue,
							'open_am' => $this->xpath->query('./open_am',$day)->item(0)->nodeValue,
							'close_am' => $this->xpath->query('./close_am',$day)->item(0)->nodeValue,
							'open_pm' => $this->xpath->query('./open_pm',$day)->item(0)->nodeValue,
							'close_pm' => $this->xpath->query('./close_pm',$day)->item(0)->nodeValue,
							);
					}
					$this->listPoints[$pointIndex] = $pointInfo;
				}
      }
    }
  }
	
}
?>
