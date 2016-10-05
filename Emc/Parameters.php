<?php
namespace Emc;

/**
* 2011-2016 Boxtal
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
* @copyright 2011-2016 Boxtal
* @license   http://www.gnu.org/licenses/
*/

class Parameters extends WebService
{

    /**
     * Public variable represents parameters array.
     * The parameters array contain the parameters for each service for each operators
     * <samp> TODO
     * Structure :<br>
     * $parameters[x]    => array(<br>
     * &nbsp;&nbsp;['name'] => data<br>
     * &nbsp;&nbsp;['code'] => data<br>
     * &nbsp;&nbsp;['services'] => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['code'] => array([...])<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;['en'] => array([...])<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;['fr'] => array([...])<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;)<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['label'] => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['type'] => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['values'] => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[x] => data)<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;)<br>
     * &nbsp;&nbsp;)<br>
     * )
     * </samp>
     * @access public
     * @var array
     */
    public $parameters = array();

    /**
     * Public function which receives the parameters list.
     * @access public
     * @param String $channel platform used (prestashop, magento etc.).
     * @param String $version platform's version.
     * @return true if request was executed correctly, false if not
     */
    public function getParameters()
    {
        $this->setGetParams(array());
        $this->setOptions(array('action' => 'api/v1/parameters'));
        if ($this->doSimpleRequest()) {
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
        //echo "<textarea>".print_r($source,true)."</textarea>";
        /* We make sure there is an XML answer and try to parse it */
        if ($source !== false) {
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
        $operators = $this->xpath->query('/operators/operator');
        $operator_data = array();
        foreach ($operators as $operator) {
            $operator_data['name'] = $this->xpath->query('name', $operator)->item(0)->nodeValue;
            $operator_data['code'] = $this->xpath->query('code', $operator)->item(0)->nodeValue;
            $operator_data['services'] = array();
            $service_data = array();
            $services = $this->xpath->query('services/service', $operator);
            foreach ($services as $service) {
                $service_data['code'] = $this->xpath->query('code', $service)->item(0)->nodeValue;
                $service_data['parameters'] = array();
                $parameters = $this->xpath->query('parameters/parameter', $service);
                $parameter_data = array();
                foreach ($parameters as $parameter) {
                    $parameter_data['code'] = array();
                    $parameter_data['code']['en'] = $this->xpath->query('code/en', $parameter)->item(0)->nodeValue;
                    $parameter_data['code']['fr'] = $this->xpath->query('code/fr', $parameter)->item(0)->nodeValue;
                    $parameter_data['label'] = $this->xpath->query('label', $parameter)->item(0)->nodeValue;
                    $parameter_data['type'] = $this->xpath->query('type', $parameter)->item(0)->nodeValue;
                    $parameter_data['values'] = array();
                    $values = $this->xpath->query('values/value', $parameter);
                    foreach ($values as $value) {
                        $parameter_data['values'][] = $value->nodeValue;
                    }
                }
                $service_data['parameters'][$parameter_data['code']['fr']] = $parameter_data;
            }
            $operator_data['services'][$service_data['code']] = $service_data;
        }
        if (isset($operator_data['code'])) {
            $this->parameters[$operator_data['code']] = $operator_data;
        }
    }
}
