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

class Env_ListPoints extends Env_WebService {

	/** 
	 * Contains Points informations.
	 *
	 * <samp>
	 * Structure :<br>
	 * $list_points[x] 	=> array(<br>
	 * &nbsp;&nbsp;['code'] 				=> data<br>
	 * &nbsp;&nbsp;['name'] 				=> data<br>
	 * &nbsp;&nbsp;['address'] 		=> data<br>
	 * &nbsp;&nbsp;['city'] 				=> data<br>
	 * &nbsp;&nbsp;['zipcode'] 		=> data<br>
	 * &nbsp;&nbsp;['country'] 		=> data<br>
	 * &nbsp;&nbsp;['description'] => data<br>
	 * &nbsp;&nbsp;['days'][x]			=> array(<br>
	 * &nbsp;&nbsp;&nbsp;&nbsp;['weekday'] 		=> data<br>
	 * &nbsp;&nbsp;&nbsp;&nbsp;['open_am'] 		=> data<br>
	 * &nbsp;&nbsp;&nbsp;&nbsp;['close_am']		=> data<br>
	 * &nbsp;&nbsp;&nbsp;&nbsp;['open_pm'] 		=> data<br>
	 * &nbsp;&nbsp;&nbsp;&nbsp;['close_pm']	 	=> data<br>
	 * &nbsp;&nbsp;)<br>
	 * )
	 * </samp>
	 * @access public
	 * @var array
	 */
	public $list_points = array();

	/**
	 * Function loads all points.
	 * @param $ope Folder ope
	 * @param $infos Parameters for the request to the api<br>
	 * <samp>
	 * Example : <br>
	 * array(<br>
	 * &nbsp;&nbsp;'srv_code' => 'RelaisColis', <br>
	 * &nbsp;&nbsp;'pays' => 'FR', <br>
	 * &nbsp;&nbsp;'cp' => '75011', <br>
	 * &nbsp;&nbsp;'ville' => 'PARIS'<br>
	 * )
	 * @access public
	 * @return Void
	 */
	public function getListPoints($ope, $infos)
	{
		$this->param = $infos;
		$this->setGetParams(array());
		$this->setOptions(array('action' => '/api/v1/'.$ope.'/listpoints'));
		$this->doListRequest();
	}

	/** 
	 * Function executes points request and prepares the $list_points array.
	 * @access private
	 * @return Void
	 */
	private function doListRequest()
	{
		$source = parent::doRequest();

		/* Uncomment if ou want to display the XML content */
		//echo '<textarea>'.$source.'</textarea>';

		/* We make sure there is an XML answer and try to parse it */
		if ($source !== false)
		{
			parent::parseResponse($source);
			if (count($this->resp_errors_list) == 0)
			{
				/* The XML file is loaded, we now gather the datas */
				$points = $this->xpath->query('/points/point');
				foreach ($points as $point_index => $point)
				{
					$point_info = array(
						'code' => $this->xpath->query('./code', $point)->item(0)->nodeValue,
						'name' => $this->xpath->query('./name', $point)->item(0)->nodeValue,
						'address' => $this->xpath->query('./address', $point)->item(0)->nodeValue,
						'city' => $this->xpath->query('./city', $point)->item(0)->nodeValue,
						'zipcode' => $this->xpath->query('./zipcode', $point)->item(0)->nodeValue,
						'country' => $this->xpath->query('./country', $point)->item(0)->nodeValue,
						'phone' => $this->xpath->query('./phone', $point)->item(0)->nodeValue,
						'description' => $this->xpath->query('./description', $point)->item(0)->nodeValue,
						'days' => array());
					$days = $this->xpath->query('./schedule/day', $point);
					foreach ($days as $day_index => $day)
					{
						$point_info['days'][$day_index] = array(
							'weekday' => $this->xpath->query('./weekday', $day)->item(0)->nodeValue,
							'open_am' => $this->xpath->query('./open_am', $day)->item(0)->nodeValue,
							'close_am' => $this->xpath->query('./close_am', $day)->item(0)->nodeValue,
							'open_pm' => $this->xpath->query('./open_pm', $day)->item(0)->nodeValue,
							'close_pm' => $this->xpath->query('./close_pm', $day)->item(0)->nodeValue);
					}
					$this->list_points[$point_index] = $point_info;
				}
			}
		}
	}
}
?>
