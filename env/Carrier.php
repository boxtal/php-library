<?php
/**
* 2007-2015 PrestaShop
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
* @copyright 2007-2015 PrestaShop SA / 2011-2015 EnvoiMoinsCher
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registred Trademark & Property of PrestaShop SA
*/

class EnvCarrier extends EnvWebService
{

    /**
     * Contains carriers array.
     *
     * <samp>
     * Structure :<br>
     * $carriers[code]    => array(<br>
     * &nbsp;&nbsp;['label']                => data<br>
     * &nbsp;&nbsp;['code']                => data<br>
     * &nbsp;&nbsp;['logo']                => data<br>
     * &nbsp;&nbsp;['logo_modules'] => data<br>
     * &nbsp;&nbsp;['description']    => data<br>
     * &nbsp;&nbsp;['address']            => data<br>
     * &nbsp;&nbsp;['url']                    => data<br>
     * &nbsp;&nbsp;['tracking']            => data<br>
     * &nbsp;&nbsp;['tel']                    => data<br>
     * &nbsp;&nbsp;['cgv']                    => data<br>
     * )
     * </samp>
     * @access public
     * @var array
     */
    public $carriers = array();

    /**
     * Function loads all carriers.
     * @return Void
     * @access public
     */
    public function getCarriers()
    {
        $this->setOptions(array('action' => '/api/v1/carriers'));
        $this->doCarrierRequest();
    }

    /**
     * Function executes carrier request and prepares the $list_points array.
     * @access private
     * @return Void
     */
    private function doCarrierRequest()
    {
        $source = $this->doRequest();

        /* We make sure there is an XML answer and try to parse it */
        if ($source !== false) {
            parent::parseResponse($source);
            if (count($this->resp_errors_list) == 0) {
                /* The XML file is loaded, we now gather the datas */
                $carriers = $this->xpath->query('/operators/operator');
                foreach ($carriers as $carrier) {
                    $result = $this->parseCarrierNode($carrier);
                    /* We use the 'code' data as index (maybe using the $c index is better) */
                    //$code = $this->xpath->query('./code', $carrier)->item(0)->nodeValue;
                    $this->carriers[$result['code']] = $result;
                }
            }
        }
    }

    protected function parseCarrierNode($carrier)
    {
        /* We usr the 'code' data as index (maybe using the $c index is better) */
        //$code = $this->xpath->query('./code', $carrier)->item(0)->nodeValue;
        $result = array(
            'label' => $this->xpath->query('./label', $carrier)->item(0)->nodeValue,
            'code' => $this->xpath->query('./code', $carrier)->item(0)->nodeValue,
            'logo' => $this->xpath->query('./logo', $carrier)->item(0)->nodeValue,
            'logo_modules' => $this->xpath->query('./logo_modules', $carrier)->item(0)->nodeValue,
            'description' => $this->xpath->query('./description', $carrier)->item(0)->nodeValue,
            'address' => $this->xpath->query('./address', $carrier)->item(0)->nodeValue,
            'url' => $this->xpath->query('./url', $carrier)->item(0)->nodeValue,
            'tracking' => $this->xpath->query('./tracking_url', $carrier)->item(0)->nodeValue,
            'tel' => $this->xpath->query('./telephone', $carrier)->item(0)->nodeValue,
            'cgv' => $this->xpath->query('./cgv', $carrier)->item(0)->nodeValue);
        return $result;
    }
}
