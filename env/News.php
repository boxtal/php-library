<?php
/** 
 * EnvoiMoinsCher API news class.
 * 
 * It can be used to load news and alerts
 * @package Env
 * @author EnvoiMoinsCher <informationAPI@envoimoinscher.com>
 * @version 1.0
 */

class Env_News extends Env_WebService
{
	/** 
	 * Public variable represents news array. 
	 * <samp>
	 * Structure :<br>
	 * $news[x]	=> array(<br>
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
		if ($this->doSimpleRequest())
		{
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
		if ($source !== false)
		{
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
		if ($news_list)
		{
			foreach ($news_list as $news)
			{
				$ad_data = array();
				$ad_data['type'] = $this->xpath->query('type', $news)->item(0)->nodeValue;
				$ad_data['message_short'] = $this->xpath->query('message_short', $news)->item(0)->nodeValue;
				$ad_data['message'] = str_replace(array('[[',']]'), array('<','>'), $this->xpath->query('message', $news)->item(0)->nodeValue);
				$this->news[] = $ad_data;
			}
		}
	}
}
?>