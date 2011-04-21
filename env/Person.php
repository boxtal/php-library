<?php
/** 
 * EnvoiMoinsCher API person class.
 * 
 * For now this class is used to initialize shipper and recipient. The next APIs' versions will
 * be able to handle other functionnalities.
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_Person extends Env_WebService {

  /** Public variable which contains recipient informations like zipcode, country and type.
   *  @access public
   *  @var array
   */
  public $recipient = array();

  /** Public variable which contains shipper informations like zipcode, country and type.
   *  @access public
   *  @var array
   */
  public $shipper = array();
   
  /** Function setter.
   *  @access public
   *  @return void
   */
  public function setPerson($object, $data) {
    $this->$object = $data;
  } 

}

?>