<?php
/** 
 * EnvoiMoinsCher API content categories class.
 * 
 * It can be used to load informations about categories or/and contents. 
 * @package Env
 * @author EnvoiMoinsCher <informationAPI@envoimoinscher.com>
 * @version 1.0
 */

class Env_ContentCategory extends Env_WebService
{

	/** 
	 * Public variable with categories array. The categories ids are the array keys.
	 * <samp>
	 * Structure :<br>
	 * $categories[code]	=> array(<br>
	 * &nbsp;&nbsp;['label']						=> data<br>
	 * &nbsp;&nbsp;['code']						=> data<br>
	 *	)
	 * </samp>
	 * @access public
	 * @var array
	 */
	public $categories = array();

	/** 
	 * Public variable with contents array. Every content element is attached to one category.
	 * <samp>
	 * Structure :<br>
	 * $contents[category][x]	=> array(<br>
	 * &nbsp;&nbsp;['code']								=> data<br>
	 * &nbsp;&nbsp;['label']								=> data<br>
	 * &nbsp;&nbsp;['category']						=> data<br>
	 * )
	 * </samp>
	 * @access public
	 * @var array
	 */
	public $contents = array();

	/** 
	 * Function loads all categories.
	 * @access public
	 * @return Void
	 */
	public function getCategories()
	{
		$this->setOptions(array('action' => '/api/v1/content_categories'));
		$this->doCatRequest();
	}

	/** 
	 * Function loads all contents.
	 * @access public
	 * @return String
	 */
	public function getContents()
	{
		$this->setOptions(array('action' => '/api/v1/contents'));
		$this->doConRequest();
	}

	/** 
	 * Function executes categories request and prepares the $categories array.
	 * @access private
	 * @return Void
	 */
	private function doCatRequest()
	{
		$source = parent::doRequest();

		/* We make sure there is an XML answer and try to parse it */
		if ($source !== false)
		{
			parent::parseResponse($source);
			if (count($this->resp_errors_list) == 0)
			{
				/* The XML file is loaded, we now gather the datas */
				$categories = $this->xpath->query('/content_categories/content_category');
				foreach ($categories as $category)
				{
					$code = $this->xpath->query('./code', $category)->item(0)->nodeValue;
					$this->categories[$code] = array(
						'label' => $this->xpath->evaluate('./label', $category)->item(0)->nodeValue,
						'code' => $code);
				}
			}
		}
	}

	/** 
	 * Function executes content request and prepares the $contents array.
	 * @access private
	 * @return Void
	 */
	private function doConRequest()
	{
		$source = parent::doRequest();

		/* We make sure there is an XML answer and try to parse it */
		if ($source !== false)
		{
			parent::parseResponse($source);
			if (count($this->resp_errors_list) == 0)
			{
				/* The XML file is loaded, we now gather the datas */
				$contents = $this->xpath->query('/contents/content');
				foreach ($contents as $content)
				{
					$category_id = $this->xpath->query('./category', $content)->item(0)->nodeValue;
					if (isset($this->contents[$category_id]))
					{
						$i = count($this->contents[$category_id]);
						$this->contents[$category_id][$i] = array(
							'code' => $this->xpath->query('./code', $content)->item(0)->nodeValue,
							'label' => $this->xpath->query('./label', $content)->item(0)->nodeValue,
							'category' => $category_id);
					}
				}
			}
		}
	}

	/** 
	 * Getter to obtain the contents of one category.
	 * @param $code : category code
	 * @access public
	 * @return Void
	 */
	public function getChild($code)
	{
		return $this->contents[$code];
	}
}

?>