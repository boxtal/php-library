<?php
/** 
 * EnvoiMoinsCher API carriers list class.
 * 
 * The class is used to obtain parameters needed for a module.
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_Parameters extends Env_WebService {

  /** 
   * Public variable represents parameters array. 
	 * <samp>
	 * Structure :<br>
	 * $parameters[x]	=> array(<br>
	 * &nbsp;&nbsp;['code'] => data<br>
	 * &nbsp;&nbsp;['label'] => data<br>
	 * &nbsp;&nbsp;['type'] => data<br>
	 * &nbsp;&nbsp;['values'] => array([...])<br>
	 * )
	 * </samp>
   * @access public
   * @var array
   */
  public $parameters = array();
	
  /** 
   * Public function which receives the carriers list. 
   * @access public
   * @param String $channel platform used (prestashop, magento etc.).
   * @param String $version platform's version.
   * @return true if request was executed correctly, false if not
   */
  public function getParameters($channel,$version)
  {
	$this->param["channel"] = strtolower($channel);
	$this->param["version"] = strtolower($version);
    $this->setGetParams(array());
    $this->setOptions(array('action' => '/api/v1/parameters'));
    if ($this->doSimpleRequest())
	{
		$this->loadParameters();
		return true;
	}
	return false;
  }

  /** 
   * Function which gets carriers list details.
   * @access private
   * @return false if server response isn't correct; true if it is
   */
  private function doSimpleRequest()
  {
    $source = parent::doRequest();	
	/* Uncomment if ou want to display the XML content */
	echo "<textarea>".print_r($source,true)."</textarea>";	
	/* We make sure there is an XML answer and try to parse it */
    if($source !== false)
	{
      parent::parseResponse($source);
      return (count($this->resp_errors_list) == 0);
    }
    return false;
  }

  /** 
   * Function load all carriers
   * @access public
   * @return Void
   */
  private function loadParameters()
  {
	$this->parameters = array();
    $parameters = $this->xpath->query('/parameters/parameter');
    foreach($parameters as $parameter)
	{
		$parameter_data = array();
		$parameter_data['code'] = $this->xpath->query('code',$parameter)->item(0)->nodeValue;
		$parameter_data['label'] = $this->xpath->query('label',$parameter)->item(0)->nodeValue;
		$parameter_data['type'] = $this->xpath->query('type',$parameter)->item(0)->nodeValue;
		$parameter_values = $this->xpath->query('values/value',$parameter);
		$parameter_data['values'] = array();
		foreach($parameter_values as $parameter_value)
		{
			$parameter_data['values'][] = $parameter_value->nodeValue;
		}
		$this->parameters[$parameter_data['code']] = $parameter_data;
    }
  }
}
?>