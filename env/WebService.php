<?php
/** 
 * EnvoiMoinsCher web service main class.
 *
 * The class handles request operations like : putting post and get parameters, calling 
 * API and getting the result of this request.
 * @package Env
 * @author EnvoiMoinsCher <dev@envoimoinscher.com>
 * @version 1.0
 */

class Env_WebService {

  /** 
   *  A public variable which determines the API server host used by curl request.
   *  @access public
   *  @var string
   */
  public $server = "https://test.envoimoinscher.com/"; // test environment by default

  /** 
   *  API test server host.
   *  @access public
   *  @var string
   */
  private $serverTest = "https://test.envoimoinscher.com/";

  /** 
   *  API production server host.
   *  @access public
   *  @var string
   */
  private $serverProd = "https://www.envoimoinscher.com/";

  /** 
   *  A private variable which stocks options to pass into curl query.
   *  @access private
   *  @var array
   */
  private $options = array();
  
  /** 
   *  A private variable with authentication credentials (login, password and api key).
   *  @access private
   *  @var array
   */
  private $auth = array();

  /** 
   *  A public variable with _POST data sent by curl function.
   *  @access public
   *  @var array
   */
  public $quotPost = array();

  /** 
   *  A public boolean which indicates if curl query was executed successful.
   *  @access public
   *  @var boolean
   */
  public $curlError = false;
  
  /** 
   *  A public variable with curl error text.
   *  @access public
   *  @var string
   */
  public $curlErrorText = "";
  
  /** 
   *  A public variable indicates if response was executed correctly.
   *  @access public
   *  @var boolean
   */
  public $respError = false;

  /** 
   *  A public variable contains error messages.
   *  @access public
   *  @var array
   */
  public $respErrorsList = array();

  /** 
   *  A public DOMXPath variable with parsed response.
   *  @access public
   *  @var DOMXPath
   */
  public $xpath = null;

  /** 
   *  A public variable determines if we have check certificate in function of your request environment.
   *  @access protected
   *  @var array
   */
  protected $sslCheck = array("peer" => true, "host" => 2);

  /**
   * Protected variable with GET parameters.
   * @access protected
   * @var string
   */
  protected $getParams = "";

  /**
   * Parameters array used by http_query_build.
   * @access protected
   * @var array
   */
  protected $param; 

  /** 
   *  Class constructor.
   *  @access public
   *  @param array $auth Array with authentication credentials.
   *  @return void
   */
  public function __construct($auth) {
    $this->auth = $auth;
  }

  /** 
   *  Function which executes api request. If an error occurs, we close curl call and put
   *  error details in $this->errorText variable. We distinguish two situations with 404 code 
   *  returned in the response : 
   *  <br />1) The API sets 404 code for valid request which doesn't contain any result. The type of response
   *     is application/xml.
   *  <br />2) The server sets 404 code too. It does it for resources which don't exist (like every 404
   *     web page). In this case the responses' type is text/html.
   *  <br />If the response returns 404 server code, we cancel the operation by setting $result to false,
   *  $respError to true and by adding an error message to $respErrorsList (with http_file_not_found value). 
   *  <br />In the case of 404 API error code, we don't break the operation. We show error messages in
   *  setResponseError().
   *  @access public
   *  @return string
   */
  public function doRequest() {
    $req = curl_init();
    curl_setopt_array($req, $this->options);
    $result = curl_exec($req);
	// You can uncomment this fragment to see the content returned by API  
	file_put_contents($_SERVER['DOCUMENT_ROOT'].'/return.xml', $result);
    $curlInfo = curl_getinfo($req);
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
                                "url" => $curlInfo["url"],
                                "message" => "Votre requête n'a pas été correctement envoyée. Veuillez vous rassurer qu'elle
                                 questionne le bon serveur (https et non pas http). Si le problème persiste, contactez notre équipe de développement");
    }
    curl_close($req); 
    return $result;
  }

  /** 
   *  Request options setter. If prod environment, sets Verisign's certificate.
   *  @access public
   *  @param array $options The request options.
   *  @return void
   */
  public function setOptions($options) {
    $this->setSSLProtection();
    $this->options = array(CURLOPT_SSL_VERIFYPEER => $this->sslCheck['peer'], CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_SSL_VERIFYHOST => $this->sslCheck['host'], CURLOPT_URL => $this->server.$options['action'].$this->getParams,
      CURLOPT_HTTPHEADER => array("Authorization: ".base64_encode($this->auth['user'].":".$this->auth['pass'])."",
      "access_key : ".$this->auth['key'].""), 
      CURLOPT_CAINFO => dirname(__FILE__).'/../ca/ca-bundle.crt'
    );
  }

  /** 
   *  It determines if CURL has to check SSL connection or not.
   *  @access private
   *  @return void
   */
  private function setSSLProtection() {
    if($this->server != "https://www.envoimoinscher.com/") {
      $this->sslCheck["peer"] = false;
      $this->sslCheck["host"] = 0;
    } 
  }
  
  /** 
   *  Function which sets the post request. 
   *  @access public
   *  @return void
   */
  public function setPost() {
    $this->options[CURLOPT_POST] = true;
    $this->options[CURLOPT_POSTFIELDS] = http_build_query($this->param);
  }
  
  /** 
   *  Function sets the get params passed into the request. 
   *  @access public
   *  @return void
   */
  public function setGetParams() {
    $this->getParams = '?'.http_build_query($this->param);
  }

  /** 
   *  Function parses api server response. 
   *  <br />First, it checks if the parsed response doesn't contain <error /> tag. If not, it does nothing.
   *  <br />Otherwise, it makes $respError parameter to true, parses the reponse and sets error messages to $respErrorsList array.
   *  @access public
   *  @param String $document The response returned by API. For use it like a XPath object, we have to 
   *                          parse it with PHPs' DOMDocument class.
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

  /** 
   *  Function detects if xml document has error tag.
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

  /** 
   *  Function sets error messages to $respErrorsList. 
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

  /**
   *  Sets environment.
   *  @access public
   *  @param string $env Server's environment : test or prod .
   *  @return void
   */
  public function setEnv($env)
  {
    $envs = array('test', 'prod');
    if(in_array($env, $envs))
    {
      $var = "server".ucfirst($env);
      $this->server = $this->$var;
    }
  }
 
  public function setParam($param)
  {
    $this->param = $param;
  }

}

?>