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

class Country extends WebService
{

    /**
     * Protected array with countries relations by ISO codes.
     * For example it contains the relation between the Canary Islands and Spain which haven't the same
     * ISO code.
     * @access protected
     * @var array
     */
    protected $codes_rel = array(
        'NL' => 'A',
        'PT' => 'P',
        'DE' => 'D',
        'IT' => 'I',
        'ES' => 'E',
        'VI' => 'V',
        'GR' => 'G');

    /**
     * Public variable with categories array. The categories codes are the array keys.
     * <samp>
     * Structure :<br>
     * $countries[code]    => array(<br>
     * &nbsp;&nbsp;['label']                => data<br>
     * &nbsp;&nbsp;['code']                    => data<br>
     * )
     * </samp>
     * @access public
     * @var array
     */
    public $countries = array();

    /**
     * Public variable with country array which contains main country and possibly the
     * iso relations.
     * <samp>
     * Structure :<br>
     * $country[x]    => array(<br>
     * &nbsp;&nbsp;['label']        => data<br>
     * &nbsp;&nbsp;['code']            => data<br>
     * )
     * </samp>
     * @access public
     * @var array
     */
    public $country = array();

    /**
     * Function loads all countries.
     * @access public
     * @return Void
     */
    public function getCountries()
    {
        $this->setOptions(array('action' => 'api/v1/countries'));
        $this->doCtrRequest();
    }

    /**
     * Function executes countries request and prepares the $countries array.
     * @access private
     * @return Void
     */
    private function doCtrRequest()
    {
        $source = parent::doRequest();

        /* We make sure there is an XML answer and try to parse it */
        if ($source !== false) {
            parent::parseResponse($source);
            if (count($this->resp_errors_list) == 0) {
                /* The XML file is loaded, we now gather the datas */
                $countries = $this->xpath->query('/countries/country');
                foreach ($countries as $country) {
                    $code = $this->xpath->query('./code', $country)->item(0)->nodeValue;
                    $this->countries[$code] = array(
                      'label' => $this->xpath->query('./label', $country)->item(0)->nodeValue,
                      'code' => $code,
                      'is_ue' => $this->xpath->query('./is_ue', $country)->item(0)->nodeValue
                    );
                }
            }
        }
    }

    /**
     * Getter function for one country. If the country code is placed in $codes_rel array,
     * we take also his relations.
     * @param $code : String with country code.
     * @access public
     * @return Void
     */
    public function getCountry($code)
    {
        $this->country = array(0 => $this->countries[$code]);
        if (isset($this->codes_rel[$code]) && $this->codes_rel[$code] != '') {
            $iso_rel = $this->codes_rel[$code];
            $i = 1;
            foreach ($this->countries as $c => $country) {
                if (preg_match('/' . $iso_rel . '\d/', $c)) {
                    $this->country[$i] = $country;
                    $i++;
                }
            }
        }
    }
}
