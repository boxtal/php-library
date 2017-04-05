<?php
/**
* 2011-2017 Boxtal
*
* NOTICE OF LICENSE
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* @author    Boxtal EnvoiMoinsCher <api@boxtal.com>
* @copyright 2011-2017 Boxtal
* @license   http://www.gnu.org/licenses/
*/

namespace Emc;

class OrderStatus extends WebService
{

    /**
     * Contains order informations.
     * <samp>
     * Structure :<br>
     * $order_info                    => array(<br>
     * &nbsp;&nbsp;['emcRef']                    => data<br>
     * &nbsp;&nbsp;['state']                    => data<br>
     * &nbsp;&nbsp;['opeRef']                    => data<br>
     * &nbsp;&nbsp;['labelAvailable']    => data<br>
     * &nbsp;&nbsp;['labelUrl']                => data<br>
     * &nbsp;&nbsp;['labels'][x]                => data<br>
     * )
     * </samp>
     * @access public
     * @var array
     */
    public $order_info = array('emcRef' => '', 'state' => '', 'opeRef' => '', 'labelAvailable' => false);

    private $documents = array('waybill' => 'bordereau', 'delivery_waybill' => 'remise');

    public $document = null;

    /**
     * Function gets order status.
     * @param $reference : order reference
     * @access public
     * @return Void
     */
    public function getOrderInformations($reference)
    {
        $this->setOptions(array('action' => 'api/v1/order_status/' . $reference . '/informations'));
        $this->doStatusRequest();
    }

    /**
     * Function gets documents for order.
     * @param $reference : array of order references
     * @param $type : document type
     * @access public
     * @return Void
     */
    public function getOrderDocuments($references, $type = 'waybill')
    {
        $this->server = $this->document_server;
        $this->param["type"] = isset($this->documents[$type]) ? $this->documents[$type] : $type;
        $this->param["envoi"] = implode(',', $references);
        $this->setGetParams();
        $this->setOptions(array('action' => ''));
        $this->document = $this->doDocumentRequest();
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
        if ($source !== false) {
            parent::parseResponse($source);
            if (count($this->resp_errors_list) == 0) {
                /* The XML file is loaded, we now gather the datas */
                $labels = array();
                $order_labels = $this->xpath->evaluate('/order/labels/*');
                foreach ($order_labels as $label_index => $label) {
                    $labels[$label_index] = $label->nodeValue;
                }
                $documents = array();
                $order_documents = $this->xpath->evaluate('/order/documents/*');
                foreach ($order_documents as $docs) {
                    $documents[$docs->nodeName] = $docs->nodeValue;
                }
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

    /**
     * Function requests document and prints it.
     * @access private
     * @return Void
     */
    private function doDocumentRequest()
    {
        $source = parent::doRequest();
        return $source;
    }
}
