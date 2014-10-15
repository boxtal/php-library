<?php
/** 
 * EnvoiMoinsCher API carrier's services class.
 * 
 * It can be used to load informations about carrier's services.
 * @package Env
 * @author EnvoiMoinsCher <informationAPI@envoimoinscher.com>
 * @version 1.0
 */

class Env_Service extends Env_Carrier {

	/**
	 * Function loads services of all carriers.
	 * @access public
	 * @return Void
	 */
	public function getServices()
	{
		$this->setOptions(array('action' => '/api/v1/services'));
		$this->doServicesRequest();
	}

	/**
	 * Function executes services request and prepares the $carriers array.
	 * @access private
	 * @return Void
	 */
	private function doServicesRequest()
	{
		$source = $this->doRequest();

		/* Uncomment if ou want to display the XML content */

		/* We make sure there is an XML answer and try to parse it */
		if ($source !== false)
		{
			parent::parseResponse($source);
			if (count($this->resp_errors_list) == 0)
			{
				/* The XML file is loaded, we now gather the datas */
				$carriers = $this->xpath->query('/operators/operator');
				foreach ($carriers as $c => $carrier)
				{
					$index = $c + 1;
					$result = $this->parseCarrierNode($carrier);
					$this->carriers[$result['code']] = $result;
					$this->carriers[$result['code']]['services'] = $this->parseServicesNode($index);
				}
			}
		}
	}

	/** 
	 * Getter for one carrier's code.
	 * @access private
	 * @return Void
	 */
	public function getServicesByCarrier($code)
	{
		if (isset($this->carriers[$code]['services']))
			return $this->carriers[$code]['services'];
		$this->setOptions(array('action' => '/api/v1/carrier/'.$code.'/services'));
		$this->doServicesRequest();
	}

	/** 
	 * Parser for service node list.
	 * <samp>
	 * Organisation :<br>
	 * $return[code] 			=> array(<br>
	 * &nbsp;&nbsp;['code'] 						=> data<br>
	 * &nbsp;&nbsp;['label'] 					=> data<br>
	 * &nbsp;&nbsp;['mode'] 						=> data<br>
	 * &nbsp;&nbsp;['alert'] 					=> data<br>
	 * &nbsp;&nbsp;['collection'] 			=> data<br>
	 * &nbsp;&nbsp;['delivery'] 				=> data<br>
	 * &nbsp;&nbsp;['is_pluggable'] 		=> data<br>
	 * &nbsp;&nbsp;['options'][code]		=> array(<br>
	 * &nbsp;&nbsp;&nbsp;&nbsp;['name'] 						=> data<br>
	 * &nbsp;&nbsp;)<br>
	 * &nbsp;&nbsp;['exclusions'][id]	=> array(<br>
	 * &nbsp;&nbsp;&nbsp;&nbsp;['label'] 				=> data<br>
	 * &nbsp;&nbsp;)<br>
	 * &nbsp;&nbsp;['apiOptions'][option][option2]	=> data<br>
	 * )
	 * </samp>
	 * @access private
	 * @param $c : Node index.
	 * @return Array Array with all available informations about the service
	 */
	private function parseServicesNode($c)
	{
		$result = array();
		$services = $this->xpath->query('/operators/operator['.$c.']/services/service');
		foreach ($services as $service)
		{
			//$s = $se + 1;
			$code = $this->xpath->query('./code', $service)->item(0)->nodeValue;
			$result[$code] = array(
				'code' => $code,
				'label' => $this->xpath->query('./label', $service)->item(0)->nodeValue,
				'mode' => $this->xpath->query('./mode', $service)->item(0)->nodeValue,
				'alert' => $this->xpath->query('./alert', $service)->item(0)->nodeValue,
				'collection' => $this->xpath->query('./collection_type', $service)->item(0)->nodeValue,
				'delivery' => $this->xpath->query('./delivery_type', $service)->item(0)->nodeValue,
				'is_pluggable' => ($this->xpath->query('./plug_available', $service)->item(0)->nodeValue == 'true' ? true : false)
			);
			$options = array();
			$exclusions = array();
			$api_options = array();
			foreach ($this->xpath->evaluate('./options/option', $service) as $option)
				$options[$this->xpath->evaluate('./code', $option)->item(0)->nodeValue] = $this->xpath->evaluate('./name', $option)->item(0)->nodeValue;
			$result[$code]['options'] = $options;
			foreach ($this->xpath->evaluate('./excluded_contents/contenu', $service) as $exclusion)
			{
				$label = $this->xpath->evaluate('./label', $exclusion)->item(0)->nodeValue;
				$exclusions[$this->xpath->evaluate('./id', $exclusion)->item(0)->nodeValue] = $label;
			}
			$result[$code]['exclusions'] = $exclusions;
			foreach ($this->xpath->evaluate('./api_options', $service) as $option)
			{
				$api_nodes = $this->xpath->evaluate('*', $option);
				for ($i = 1; $i < $api_nodes->length; $i++)
				{
					$api_node = $api_nodes->item($i);
					$api_node_childs = $this->xpath->evaluate('*', $api_node);
					$api_options[$api_node->nodeName] = array();
					for ($a = 1; $a < $api_node_childs->length; $a++)
					{
						$api_options[$api_node->nodeName][$api_node_childs->item($a)->nodeName] = $api_node_childs->item($a)->nodeValue;
						$a++;
					}
				}
				$i++;
			}
			$result[$code]['apiOptions'] = $api_options;
		}
		return $result;
	}
}
?>