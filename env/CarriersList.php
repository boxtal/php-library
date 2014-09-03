<?php
/**
 * 2007-2014 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    EnvoiMoinsCher <informationapi@boxtale.com>
 * @copyright 2007-2014 PrestaShop SA / 2011-2014 EnvoiMoinsCher
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of PrestaShop SA
 */

class Env_CarriersList extends Env_WebService
{
	/** 
	 * Public variable represents offers array. 
	 * <samp>
	 * Structure :<br>
	 * $carriers[x]	=> array(<br>
	 * &nbsp;&nbsp;['ope_code'] => data<br>
	 * &nbsp;&nbsp;['ope_name'] => data<br>
	 * &nbsp;&nbsp;['srv_code'] => data<br>
	 * &nbsp;&nbsp;['label_store'] => data<br>
	 * &nbsp;&nbsp;['description'] => data<br>
	 * &nbsp;&nbsp;['description_store'] => data<br>
	 * &nbsp;&nbsp;['family'] => data<br>
	 * &nbsp;&nbsp;['zone'] => data<br>
	 * &nbsp;&nbsp;['parcel_pickup_point'] => data<br>
	 * &nbsp;&nbsp;['parcel_dropoff_point'] => data<br>
	 * )
	 * </samp>
	 * @access public
	 * @var array
	 */
	public $carriers = array();

	/** 
	 * Public function which receives the carriers list. 
	 * @access public
	 * @param String $channel platform used (prestashop, magento etc.).
	 * @param String $version platform's version.
	 * @return true if request was executed correctly, false if not
	 */
	public function getCarriersList($channel, $version)
	{
		$this->param['channel'] = $channel;
		$this->param['version'] = $version;
		$this->setGetParams(array());
		$this->setOptions(array('action' => '/api/v1/carriers_list'));
		if ($this->doSimpleRequest())
		{
			$this->getCarriersList();
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

		/* We make sure there is an XML answer and try to parse it */
		if ($source !== false)
		{
			parent::parseResponse($source);
			return (count($this->resp_errors_list) == 0);
		}
		return false;
	}

	/** 
	 * Function load all carriers
	 * @access public
	 * @param bool $only_com If true, we have to get only offers in the 'order' mode.
	 * @return Void
	 */
	private function loadCarriersList()
	{
		$this->carriers = array();
		$operators = $this->xpath->query('/operators/operator');
		foreach ($operators as $operator)
		{
			$ope_code = $this->xpath->query('./code', $operator)->item(0)->nodeValue;
			$ope_name = $this->xpath->query('./name', $operator)->item(0)->nodeValue;
			$ope_carriers = $this->xpath->query('./services/service', $operator);
			foreach ($ope_carriers as $carrier)
			{
				$id = count($this->carriers);
				$this->carriers[$id]['ope_code'] = $ope_code;
				$this->carriers[$id]['ope_name'] = $ope_name;
				$this->carriers[$id]['srv_code'] = $this->xpath->query('./code', $carrier)->item(0)->nodeValue;
				$this->carriers[$id]['srv_name'] = $this->xpath->query('./label', $carrier)->item(0)->nodeValue;
				$this->carriers[$id]['label_store'] = $this->xpath->query('./label_store', $carrier)->item(0)->nodeValue;
				$this->carriers[$id]['description'] = $this->xpath->query('./description', $carrier)->item(0)->nodeValue;
				$this->carriers[$id]['description_store'] = $this->xpath->query('./description_store', $carrier)->item(0)->nodeValue;
				$this->carriers[$id]['family'] = $this->xpath->query('./family', $carrier)->item(0)->nodeValue;
				$this->carriers[$id]['zone'] = $this->xpath->query('./zone', $carrier)->item(0)->nodeValue;
				$this->carriers[$id]['parcel_pickup_point'] = $this->xpath->query('./parcel_pickup_point', $carrier)->item(0)->nodeValue;
				$this->carriers[$id]['parcel_dropoff_point'] = $this->xpath->query('./parcel_dropoff_point', $carrier)->item(0)->nodeValue;
			}
		}
	}
}
?>