<?php
/**
* 2011-2015 Boxtale
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
* @author    Boxtale EnvoiMoinsCher <informationapi@boxtale.com>
* @copyright 2011-2015 Boxtale
* @license   http://www.gnu.org/licenses/
*/

class EnvListPoints extends EnvWebService
{

    /**
     * Contains Points informations.
     *
     * <samp>
     * Structure :<br>
     * $list_points[x]    => array(<br>
     * &nbsp;&nbsp;['code']                => data<br>
     * &nbsp;&nbsp;['name']                => data<br>
     * &nbsp;&nbsp;['address']        => data<br>
     * &nbsp;&nbsp;['city']                => data<br>
     * &nbsp;&nbsp;['zipcode']        => data<br>
     * &nbsp;&nbsp;['country']        => data<br>
     * &nbsp;&nbsp;['description'] => data<br>
     * &nbsp;&nbsp;['days'][x]            => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['weekday']        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['open_am']        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['close_am']        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['open_pm']        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['close_pm']        => data<br>
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
        $this->setOptions(array('action' => '/api/v1/' . $ope . '/listpoints'));
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
        if ($source !== false) {
            parent::parseResponse($source);
            if (count($this->resp_errors_list) == 0) {
                /* The XML file is loaded, we now gather the datas */
                $points = $this->xpath->query('/points/point');
                foreach ($points as $point_index => $point) {
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
                    foreach ($days as $day_index => $day) {
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
