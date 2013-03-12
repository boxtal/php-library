<?php
/** 
 * EnvoiMoinsCher API user class.
 * 
 * Actually can be used only to configure e-mail send by EnvoiMoinsCher.com to user, shipper and receiver. 
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_User extends Env_WebService {

  /**
   * Array with user configuration informations. Actually we put only email informations.
   * @access public
   * @var array
   */
  public $userConfiguration = array("emails" => array());

  /**
   * Gets information about e-mail configuration for logged user.
   * @access public
   * @return void
   */
  public function getEmailConfiguration() {
    $this->setOptions(array("action" => "/api/v1/emails_configuration"));
    $this->setEmailConfiguration();
  }

  /**
   * Posts new informations about e-mail configuration for logged user.
   * Accepted keys are : label, notification, bill. If you want to remove the e-mail sending
   * for one of these keys, you must put into it an empty string like "".
   * @access public
   * @param array $params Params with new e-mail configuration
   * @return void
   */
  public function postEmailConfiguration($params) {
    $this->setOptions(array("action" => "/api/v1/emails_configuration"));
    $this->param = $params;
    $this->setPost();
    $this->setEmailConfiguration();
  }

  /**
   * Parses API response and puts the values into e-mail configuration array.
   * @access private
   * @return void
   */
  private function setEmailConfiguration() {
    $source = parent::doRequest();
    if($source !== false) {
      parent::parseResponse($source);
      foreach($this->xpath->evaluate("/user/mails")->item(0)->childNodes as $configLine) {
        if(!($configLine instanceof DOMText)) {
          $this->userConfiguration["emails"][$configLine->nodeName] = $configLine->nodeValue;
        }
      }
    }
  }

}

?>