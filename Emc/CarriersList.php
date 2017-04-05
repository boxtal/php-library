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

class CarriersList extends WebService
{
    /**
     * Public variable represents offers array.
     * <samp>
     * Structure :<br>
     * $carriers[x]    => array(<br>
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
    * [__construct description]
    */
    public function __construct()
    {
        parent::__construct();
        $this->param['channel'] = $this->platform;
        $this->param['version'] = $this->platform_version;
    }

    /**
     * Public function which receives the carriers list.
     * @access public
     * @param String $channel platform used (prestashop, magento etc.).
     * @param String $version platform's version.
     * @return true if request was executed correctly, false if not
     */
    public function getCarriersList()
    {
        $this->setGetParams(array());
        $this->setOptions(array('action' => 'api/v1/carriers_list'));
        if ($this->doSimpleRequest()) {
            $this->loadCarriersList();
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
        if ($source !== false) {
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
        foreach ($operators as $operator) {
            $ope_code = $this->xpath->query('./code', $operator)->item(0)->nodeValue;
            $ope_name = $this->xpath->query('./name', $operator)->item(0)->nodeValue;
            $ope_cgv = $this->xpath->query('./cgv', $operator)->item(0)->nodeValue;
            $ope_carriers = $this->xpath->query('./services/service', $operator);
            foreach ($ope_carriers as $carrier) {
                $id = count($this->carriers);
                $this->carriers[$id]['ope_code'] = $ope_code;
                $this->carriers[$id]['ope_name'] = $ope_name;
                $this->carriers[$id]['ope_cgv'] = $ope_cgv;
                $this->carriers[$id]['srv_code'] = $this->xpath->query('./code', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['srv_name_fo'] =
                  $this->xpath->query('./srv_name_fo', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['srv_name_bo'] =
                  $this->xpath->query('./description_store', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['old_srv_name'] = $this->xpath->query('./label', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['description'] =
                  $this->xpath->query('./description', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['family'] = $this->xpath->query('./family', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['zone'] = $this->xpath->query('./zone', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['zone_fr'] = $this->xpath->query('./zone_fr', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['zone_es'] = $this->xpath->query('./zone_es', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['zone_eu'] = $this->xpath->query('./zone_eu', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['zone_int'] = $this->xpath->query('./zone_int', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['zone_restriction'] =
                  $this->xpath->query('./zone_restriction', $carrier)->item(0)->nodeValue;
                foreach ($this->xpath->query('./details/detail', $carrier) as $detail) {
                    $this->carriers[$id]['details'][] = $detail->nodeValue;
                }
                $this->carriers[$id]['delivery_due_time'] =
                  $this->xpath->query('./delivery_due_time', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['delivery_type'] =
                  $this->xpath->query('./delivery_type', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['parcel_pickup_point'] =
                  $this->xpath->query('./parcel_pickup_point', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['parcel_dropoff_point'] =
                  $this->xpath->query('./parcel_dropoff_point', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['pickup_place'] =
                  $this->xpath->query('./pickup_place', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['dropoff_place'] =
                  $this->xpath->query('./dropoff_place', $carrier)->item(0)->nodeValue;
                $this->carriers[$id]['allowed_content'] = array();
                foreach ($this->xpath->query('./inclusion_content/content', $carrier) as $content) {
                    $idNode = $this->xpath->query('./id', $content);
                    $labelNode = $this->xpath->query('./label', $content);
                    $conditionNode = $this->xpath->query('./condition', $content);

                    if ($idNode->length == 1 && $labelNode->length == 1 && $conditionNode->length <= 1) {
                        $contId = $idNode->item(0)->nodeValue;
                        $condition = ($conditionNode->length > 0) ? $conditionNode->item(0)->nodeValue : null;
                        $label = $labelNode->item(0)->nodeValue;
                        $this->carriers[$id]['allowed_content'][$contId] = array(
                          'id' => $contId,
                          'condition' => $condition,
                          'label' => $label
                        );
                    }
                }
                $this->carriers[$id]['srv_cgv'] =
                  $this->xpath->query('./cgv', $carrier)->item(0)->nodeValue;
                foreach ($this->xpath->query('./translations/translation', $carrier) as $translation) {
                    $locale = $this->xpath->query('./locale', $translation)->item(0)->nodeValue;
                    $this->carriers[$id]['translations']['srv_name_fo'][$locale] =
                      $this->xpath->query('./srv_name_fo', $translation)->item(0)->nodeValue;
                    $this->carriers[$id]['translations']['srv_name_bo'][$locale] =
                      $this->xpath->query('./description_store', $translation)->item(0)->nodeValue;
                    $this->carriers[$id]['translations']['description'][$locale] =
                      $this->xpath->query('./description', $translation)->item(0)->nodeValue;
                    $this->carriers[$id]['translations']['zone_restriction'][$locale] =
                      $this->xpath->query('./zone_restriction', $translation)->item(0)->nodeValue;
                    foreach ($this->xpath->query('./details/detail', $translation) as $detail) {
                        $this->carriers[$id]['translations']['details'][$locale][] = $detail->nodeValue;
                    }
                    $this->carriers[$id]['translations']['delivery_due_time'][$locale] =
                      $this->xpath->query('./delivery_due_time', $translation)->item(0)->nodeValue;
                    $this->carriers[$id]['translations']['pickup_place'][$locale] =
                      $this->xpath->query('./pickup_place', $translation)->item(0)->nodeValue;
                    $this->carriers[$id]['translations']['dropoff_place'][$locale] =
                      $this->xpath->query('./dropoff_place', $translation)->item(0)->nodeValue;
                }
            }
        }
    }
}
