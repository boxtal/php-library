<?php
/** 
 * EnvoiMoinsCher API content categories class.
 * 
 * It can be used to load informations about categories or/and contents. 
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_ContentCategory extends Env_WebService {

  /** 
   *  Public variable with categories array. The categories ids are the array keys.
   *  Organisation :
	 *	$categories[code]	=> array(
	 *		['label']						=> data
	 *		['code']						=> data
	 *	)
   *  @access public
   *  @var array
   */
  public $categories = array();

  /** 
   *  Public variable with contents array. Every content element is attached to one category by
   *  Organisation :
	 *	$contents[category][x]	=> array(
	 *		['code']								=> data
	 *		['label']								=> data
	 *		['category']						=> data
	 *	)
   *  @access public
   *  @var array
   */
  public $contents = array();

  /** 
   *  Function loads all categories.
   *  @access public
   *  @return void
   */
  public function getCategories() { 
    $this->setOptions(array("action" => "/api/v1/content_categories",
		));
    $this->doCatRequest();
  }

  /** 
   *  Function loads all contents.
   *  @access public
   *  @return string
   */
  public function getContents() { 
    $this->setOptions(array("action" => "/api/v1/contents",
		));
    $this->doConRequest();
  }
  
  /** 
   *  Function executes categories request and prepares the $categories array.
   *  @access private
   *  @return void
   */
  private function doCatRequest() {
    $source = parent::doRequest();
		
		/* Uncomment if ou want to display the XML content */
		//echo '<textarea>'.$source.'</textarea>';
		
		/* We make sure there is an XML answer and try to parse it */
    if($source !== false) {
      parent::parseResponse($source);
	  	if(count($this->respErrorsList) == 0) {
				
				/* The XML file is loaded, we now gather the datas */
				$categories = $this->xpath->query("/content_categories/content_category");
				foreach($categories as $c => $category) {
					$code = $this->xpath->query("./code",$category)->item(0)->nodeValue;
					$this->categories[$code] = array(
						'label' => $this->xpath->evaluate("./label",$category)->item(0)->nodeValue,
						'code' => $code
						);
				}
			}
    }
  }

  /** 
   *  Function executes content request and prepares the $contents array.
   *  @access private
   *  @return void
   */
  private function doConRequest() {
    $source = parent::doRequest();
		
		/* Uncomment if ou want to display the XML content */
		//echo '<textarea>'.$source.'</textarea>';
		
		/* We make sure there is an XML answer and try to parse it */
    if($source !== false) {
      parent::parseResponse($source);
	  	if(count($this->respErrorsList) == 0) {
				
				/* The XML file is loaded, we now gather the datas */
				$contents = $this->xpath->query("/contents/content");
				foreach($contents as $c => $content) {
					$categoryId = $this->xpath->query('./category',$content)->item(0)->nodeValue;
					$i = count($this->contents[$categoryId]);
					$this->contents[$categoryId][$i] = array(
						'code' => $this->xpath->query('./code',$content)->item(0)->nodeValue,
						'label' => $this->xpath->query('./label',$content)->item(0)->nodeValue,
						'category' => $categoryId
						);
				}
			}
    }
  }

  /** 
   *  Getter to obtain the contents of one category.
	 *  @param $code : category code
   *  @access public
   *  @return void
   */
  public function getChild($code) {
    return $this->contents[$code];
  }

}

?>