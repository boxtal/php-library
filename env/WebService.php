<?php
/** 
 * EnvoiMoinsCher web service main class.
 *
 * The class handles request operations like : putting post and get parameters, calling 
 * API and getting the result of this request.
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_WebService {

  /** A protected variable which determines the API server host.
   *  @access protected
   *  @var string
   */
  protected $server = "http://localhost:8080";

  /** A private variable which stocks options to pass into curl query.
   *  @access private
   *  @var array
   */
  private $options = array();
  
  /** A private variable with authentication credentials (login, password and api key).
   *  @access private
   *  @var array
   */
  private $auth = array();

  /** A public variable with _POST data sent by curl function.
   *  @access public
   *  @var array
   */
  public $quotPost = array();

  /** A public boolean which indicates if curl query was executed successful.
   *  @access public
   *  @var boolean
   */
  public $curlError = false;
  
  /** A public variable with curl error text.
   *  @access public
   *  @var string
   */
  public $curlErrorText = "";
  
  /** A public variable indicates if response was executed correctly.
   *  @access public
   *  @var boolean
   */
  public $respError = false;

  /** A public variable contains error messages.
   *  @access public
   *  @var array
   */
  public $respErrorsList = array();

  /** A public DOMXPath variable with parsed response.
   *  @access public
   *  @var DOMXPath
   */
  public $xpath = null;

  /** Class constructor.
   *  @access public
   *  @param array $auth Array with authentication credentials.
   *  @return void
   */
  public function __construct($auth) {
    $this->auth = $auth;
  }

  /** Function which executes api request. If an error occurs, we close curl call and put 
   *  error details in $this->errorText variable. We distinguish two situations with 404 code 
   *  returned in the response : 
   *  1) The API sets 404 code for valid request which doesn't contain any result. The type of response
   *     is application/xml.
   *  2) The server sets 404 code too. It does it for resources which don't exist (like every 404
   *     web page). In this case the responses' type is text/html.
   *  If the response returns 404 server code, we cancel the operation by setting $result to false,
   *  $respError to true and by adding an error message to $respErrorsList (with http_file_not_found value). 
   *  In the case of 404 API error code, we don't break the operation. We show error messages in
   *  setResponseError().
   *  @access public
   *  @return string
   */
  public function doRequest() {
    $req = curl_init();
    curl_setopt_array($req, $this->options);
    $result = curl_exec($req);    //echo '<br /><br />'.$result; 
	 file_put_contents($_SERVER['DOCUMENT_ROOT'].'/return.xml', $result);
    $curlInfo = curl_getinfo($req);   //print_r(curl_getinfo($req));
    $contentType = explode(";", $curlInfo["content_type"]);
    if(curl_errno($req) > 0) {
      $this->curlError = true;
      $this->curlErrorText = curl_error($req);
      curl_close($req); 
      return false;
    }
    elseif(trim($contentType[0]) == "text/html" && $curlInfo["http_code"] == "404") {
      $result = false;
      $this->respError = true;
      $i = 0;
      if($this->constructList) {
        $i = count($this->respErrorsList);
      }
      $this->respErrorsList[$i] = array("code" => "http_file_not_found",
                                "url" => $curlInfo["url"]);
    }
    curl_close($req); 
    return $result;
  }

  /** Request options setter. 
   *  @access public
   *  @param array $options The request options.
   *  @return void
   */
  public function setOptions($options) {
    $this->options = array(CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $this->server.$options['action'].$this->getParams,
      CURLOPT_HTTPHEADER => array("Authorization: ".base64_encode($this->auth['user'].":".$this->auth['pass'])."",
      "access_key : ".$this->auth['key']."")
    ); 
  }
  
  /** Function which sets the post request. 
   *  @access public
   *  @return void
   */
  public function setPost() {
    $this->options[CURLOPT_POST] = true;
    $this->options[CURLOPT_POSTFIELDS] = http_build_query($this->param);   //echo '<br /><br />'.http_build_query($this->param);
  }
  
  /** Function sets the get params passed into the request. 
   *  @access public
   *  @return void
   */
  public function setGetParams() {
    $this->getParams = '?'.http_build_query($this->param);
  }

  /** Function parses api server response. 
   *  First, it checks if the parsed response doesn't contain <error /> tag. If not, it does nothing.
   *  Otherwise, it makes $respError parameter to true, parses the reponse and sets error messages to $respErrorsList array.
   *  @access public
   *  @param String $document The response returned by API. For use it like a XPath object, we have to 
   *                          parse it with PHP's DOMDocument class.
   *  @return void
   */
  public function parseResponse($document) {
    $domCl = new DOMDocument();
    $domCl->loadXML($document);
    $this->xpath = new DOMXPath($domCl);
    if($this->hasErrors()) {
      $this->setResponseErrors();
    }
  }

  /** Function detects if xml document has error tag. 
   *  @access private
   *  @return boolean true if xml document has error tag, false if it hasn't.
   */
  private function hasErrors() {
    if((int)$this->xpath->evaluate("count(/error)") > 0) {
      $this->respError = true;
      return true;
    }
    return false;
  }

  /** Function sets error messages to $respErrorsList. 
   *  @access private
   *  @return boolean true if xml document has error tag, false if it hasn't.
   */
  private function setResponseErrors() {
    $errors = $this->xpath->evaluate("/error");
    foreach($errors as $e => $error) {
      $this->respErrorsList[$e] = array("code" => $this->xpath->evaluate(".//code")->item($e)->nodeValue,
                                        "message" => $this->xpath->evaluate(".//message")->item($e)->nodeValue
                                  );
    }
  }

}

?>