<?php
/** 
 * EnvoiMoinsCher API countries class.
 * 
 * It can be used to download and manipulate one or more countries.
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_Country extends Env_WebService {

  /** 
   *  Protected array with countries relations by ISO codes.
   *  <br />For example it contains the relation between the Canary Islands and Spain which haven't the same 
   *  ISO code.
   *  @access protected
   *  @var array
   */
  protected $codesRel = array("NL" => "A", "PT" => "P", "DE" => "D", "IT" => "I", "ES" => "E", 
                              "VI" => "V", "GR" => "G");

  /** 
   *  Public variable with categories array. The categories codes are the array keys. 
   *  @access public
   *  @var array
   */
  public $countries = array();

  /** 
   *  Public variable with country array which contains main country and possibly the 
   *  iso relations.
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
   *  Function executes getCountries() request and prepares the $countries array.
   *  @access private
   *  @return void
   */
  private function doCtrRequest() {
    $source = parent::doRequest();
    if($source !== false) {
      parent::parseResponse($source);
      $countries = $this->xpath->query("/countries/country");
      foreach($countries as $c => $country) {
        $code = $this->xpath->evaluate(".//code")->item($c)->nodeValue;
        $this->countries[$code] = array("label" => $this->xpath->evaluate(".//label")->item($c)->nodeValue,
          "code" => $code);
      }
    }
  }

  /** 
   *  Getter function for one country. If the country code is placed in $codesRel array, 
   *  we take also his relations.
   *  @access public
   *  @param string $code String with country code.
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