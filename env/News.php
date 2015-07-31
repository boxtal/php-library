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

class EnvNews extends EnvWebService
{
    /**
     * Public variable represents news array.
     * <samp>
     * Structure :<br>
     * $news[x]    => array(<br>
     * &nbsp;&nbsp;['type'] => data<br>
     * &nbsp;&nbsp;['message_short'] => data<br>
     * &nbsp;&nbsp;['message'] => data<br>
     * )
     * </samp>
     * @access public
     * @var array
     */
    public $news = array();

    /**
     * Public function which receives the news list.
     * @access public
     * @param String $channel platform used (prestashop, magento etc.).
     * @param String $version platform's version.
     * @return true if request was executed correctly, false if not
     */
    public function loadNews($channel, $version)
    {
        $this->param['channel'] = $channel;
        $this->param['version'] = $version;
        $this->setGetParams(array());
        $this->setOptions(array('action' => '/api/v1/news'));
        if ($this->doSimpleRequest()) {
            $this->getNews();
            return true;
        }
        return false;
    }

    /**
     * Function which gets news list details.
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
     * Function load all news
     * @access public
     * @return Void
     */
    public function getNews()
    {
        $this->news = array();
        $news_list = $this->xpath->query('/flux/news');
        if ($news_list) {
            foreach ($news_list as $news) {
                $ad_data = array();
                $ad_data['type'] = $this->xpath->query('type', $news)->item(0)->nodeValue;
                $ad_data['message_short'] = $this->xpath->query('message_short', $news)->item(0)->nodeValue;
                $ad_data['message'] = str_replace(
                    array('[[', ']]'),
                    array('<', '>'),
                    $this->xpath->query('message', $news)->item(0)->nodeValue
                );
                $this->news[] = $ad_data;
            }
        }
    }
}
