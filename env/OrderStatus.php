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
 * versions in the future. If you wish to customize this library for your
 * needs please refer to http://www.envoimoinscher.com for more information.
 *
 * @author    EnvoiMoinsCher <informationapi@boxtale.com>
 * @copyright 2011-2014 EnvoiMoinsCher
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of EnvoiMoinsCher
 */

class Env_OrderStatus extends Env_WebService
{

	/** 
	 * Contains order informations.
	 * <samp>
	 * Structure :<br>
	 * $order_info		 			=> array(<br>
	 * &nbsp;&nbsp;['emcRef'] 					=> data<br>
	 * &nbsp;&nbsp;['state'] 					=> data<br>
	 * &nbsp;&nbsp;['opeRef'] 					=> data<br>
	 * &nbsp;&nbsp;['labelAvailable']	=> data<br>
	 * &nbsp;&nbsp;['labelUrl'] 				=> data<br>
	 * &nbsp;&nbsp;['labels'][x]				=> data<br>
	 * )
	 * </samp>
	 * @access public
	 * @var array
	 */
	public $order_info = array('emcRef' => '', 'state' => '', 'opeRef' => '', 'labelAvailable' => false);

	/**
	 * Function loads all categories.
	 * @param $reference : folder reference
	 * @access public
	 * @return Void
	 */
	public function getOrderInformations($reference)
	{
		$this->setOptions(array('action' => '/api/v1/order_status/'.$reference.'/informations'));
		$this->doStatusRequest();
	}

	/** 
	 * Function executes order request and prepares the $order_info array.
	 * @access private
	 * @return Void
	 */
	private function doStatusRequest()
	{
		$source = parent::doRequest();

		/* We make sure there is an XML answer and try to parse it */
		if ($source !== false)
		{
			parent::parseResponse($source);
			if (count($this->resp_errors_list) == 0)
			{
				/* The XML file is loaded, we now gather the datas */
				$labels = array();
				$order_labels = $this->xpath->evaluate('/order/labels/*');
				foreach ($order_labels as $label_index => $label)
					$labels[$label_index] = $label->nodeValue;
				$documents = array();
				$order_documents = $this->xpath->evaluate('/order/documents/*');
				foreach ($order_documents as $docs)
					$documents[$docs->nodeName] = $docs->nodeValue;
				$this->order_info = array(
					'emcRef' => $this->xpath->evaluate('/order/emc_reference')->item(0)->nodeValue,
					'state' => $this->xpath->evaluate('/order/state')->item(0)->nodeValue,
					'opeRef' => $this->xpath->evaluate('/order/carrier_reference')->item(0)->nodeValue,
					'labelAvailable' => (bool)$this->xpath->evaluate('/order/label_available')->item(0)->nodeValue,
					'labelUrl' => $this->xpath->evaluate('/order/label_url')->item(0)->nodeValue,
					'labels' => $labels,
					'documents' => $documents);
			}
		}
	}
}

?>
