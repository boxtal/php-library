<?php
/** 
 * EnvoiMoinsCher API countries class.
 * 
 * It can be used to load informations about one or more countries.
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_Country extends Env_WebService {

  /** 
   *  Protected array with countries relations by ISO codes.
   *  For example it contains the relation between the Canary Islands and Spain which haven't the same 
   *  ISO code.
   *  @access protected
   *  @var array
   */
  protected $codesRel = array("NL" => "A", "PT" => "P", "DE" => "D", "IT" => "I", "ES" => "E", 
                              "VI" => "V", "GR" => "G");

  /** 
   *  Public variable with categories array. The categories codes are the array keys. 
	 *  Organisation :
	 *	$countries[code] 	=> array(
	 *  	['label'] 				=> data
	 *  	['code'] 					=> data
	 *  )
   *  @access public
   *  @var array
   */
  public $countries = array();

  /** 
   *  Public variable with country array which contains main country and possibly the 
   *  iso relations.
	 *  Organisation :
	 *	$country[x] 	=> array(
	 *  	['label'] 		=> data
	 *  	['code'] 			=> data
	 *  )
   *  @access public
   *  @var array
   */
  public $country = array();

  /** 
   *  Function loads all countries.
   *  @access public
   *  @return void
   */
  public function getCountries() { 
    $this->setOptions(array("action" => "/api/v1/countries",
		)); 
    $this->doCtrRequest();
  }
  
  /** 
   *  Function executes countries request and prepares the $countries array.
   *  @access private
   *  @return void
   */
  private function doCtrRequest() {
    $source = parent::doRequest();
		
		/* Uncomment if ou want to display the XML content */
		//echo '<textarea>'.$source.'</textarea>';
		
		/* We make sure there is an XML answer and try to parse it */
    if($source !== false) {
      parent::parseResponse($source);
	  	if(count($this->respErrorsList) == 0) {
				
				/* The XML file is loaded, we now gather the datas */
				$countries = $this->xpath->query("/countries/country");
				foreach($countries as $c => $country) {
					$code = $this->xpath->query("./code",$country)->item(0)->nodeValue;
					$this->countries[$code] = array(
						'label' => $this->xpath->query('./label',$country)->item(0)->nodeValue,
						'code' => $code
						);
				}
			}
    }
  }

  /** 
   *  Getter function for one country. If the country code is placed in $codesRel array, 
   *  we take also his relations.
   *  @param $code : String with country code.
   *  @access public
   *  @return void
   */
  public function getCountry($code) {
    $this->country = array(0 => $this->countries[$code]);
    $isoRel = $this->codesRel[$code];
    if($isoRel != "") {
      $i = 1;
      foreach($this->countries as $c => $country) {
        if(preg_match("/$isoRel\d/", $c)) {
          $this->country[$i] = $country;
          $i++;
        }
      }
    }
  }


}

?>