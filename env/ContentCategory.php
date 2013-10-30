<?php
/** 
 * EnvoiMoinsCher API content categories class.
 * 
 * It can be used to load categories or/and contents. 
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_ContentCategory extends Env_WebService {

  /** 
   *  Public variable with categories array. The categories ids are the array keys.
   *  For exemple, one category has following values : 
   *  <br />- id : 1111
   *  <br />- code : A
   *  <br />- label : First category A 
   *  <br />The PHP array corresponding to these values : array(1111 => array("label" => "First category A",
   *  "code" => "A"))
   *  @access public
   *  @var array
   */
  public $categories = array();

  /** 
   *  Public variable with contents array. Every content element is attached to one category by
   *  category id. For exemple, our category 1111 will have following contents : 
   *  $categories[1111] = array(0 => array("code" => 11112, "label" => "code 11112"), 1 => array("code" => 11113, "label" => "code 11113"))
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
    if($source !== false) {
      parent::parseResponse($source);
      $contents = $this->xpath->query("/content_categories/content_category");
      foreach($contents as $c => $content) {
        $code = $this->xpath->evaluate(".//code")->item($c)->nodeValue;
        $this->categories[$code] = array("label" => $this->xpath->evaluate(".//label")->item($c)->nodeValue,
        "code" => $code);
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
    if($source !== false) {
      parent::parseResponse($source);
      $contents = $this->xpath->query("/contents/content");
      foreach($contents as $c => $content) {
        $categoryId = $this->xpath->evaluate(".//category")->item($c)->nodeValue;
        $i = count($this->contents[$categoryId]);
        $this->contents[$categoryId][$i] = array(
          "code" => $this->xpath->evaluate(".//code")->item($c)->nodeValue,
          "label" => $this->xpath->evaluate(".//label")->item($c)->nodeValue,
          "category" => $categoryId
        );
      }
    }
  }

  /** 
   *  Class getter to obtain the contents of one category.
   *  @access public
   *  @return void
   */
  public function getChild($code) {
    return $this->contents[$code];
  }

}

?>