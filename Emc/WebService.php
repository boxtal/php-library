<?php
/**
* 2011-2023 Boxtal
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
* @author    Boxtal <api@boxtal.com>
* @copyright 2011-2023 Boxtal
* @license   http://www.gnu.org/licenses/
*/

namespace Emc;

define('ENV_TEST', 'test');
define('ENV_PRODUCTION', 'prod');

class WebService
{

    /**
     * A public variable which determines the API server host used by curl request.
     * @access public
     * @var string
     */
    public $server = 'https://test.envoimoinscher.com/';

    /**
     * API test server host.
     * @access public
     * @var string
     */
    private $server_test = 'https://test.envoimoinscher.com/';

    /**
     * API production server host.
     * @access public
     * @var string
     */
    private $server_prod = 'https://www.envoimoinscher.com/';

    /**
     * A public variable which determines the document server used by curl request.
     * @access public
     * @var string
     */
    public $document_server = 'https://test.envoimoinscher.com/documents';

    /**
     * API test document server host.
     * @access public
     * @var string
     */
    private $document_server_test = 'https://test.envoimoinscher.com/documents';

    /**
     * API production document server host.
     * @access public
     * @var string
     */
    private $document_server_prod = 'https://documents.envoimoinscher.com/documents';

    /**
     * Library version
     * @access protected
     * @var string
     */
    protected $api_version = '1.3.7';

    /**
     * A private variable which stocks options to pass into curl query.
     * @access private
     * @var array
     */
    private $options = array();

    /**
     * A private variable with authentication credentials (login, password).
     * @access private
     * @var array
     */
    private $auth = array();

    /**
     * A public boolean which indicates if curl query was executed successful.
     * @access public
     * @var boolean
     */
    public $curl_error = false;

    /**
     * A public int which indicates wich curl error number we reach
     * @access public
     * @var integer
     */
    public $curl_errno = null;

    /**
     * A public variable with curl error text.
     * @access public
     * @var string
     */
    public $curl_error_text = '';

    /**
     * A public variable indicates if response was executed correctly.
     * @access public
     * @var boolean
     */
    public $resp_error = false;

    /**
     * A public variable contains error messages.
     * @access public
     * @var array
     */
    public $resp_errors_list = array();

    /**
     * A public DOMXPath variable with parsed response.
     * @access public
     * @var DOMXPath
     */
    public $xpath = null;

    /**
     * curl Timeout
     * @access public
     * @var int
     */
    public $timeout =  null;

    /**
     * A public variable determines if we have check certificate in function of your request environment.
     * @access protected
     * @var array
     */
    protected $ssl_check = array('peer' => true, 'host' => 2);

    /**
     * Protected variable with GET parameters.
     * @access protected
     * @var mixed
     */
    protected $get_params;

    /**
     * Parameters array used by http_query_build.
     * @access protected
     * @var array
     */
    protected $param;

    /**
     * Parameters array used by http_query_build for curl multi request.
     * @access protected
     * @var array
     */
    protected $param_multi = array();

    /**
     * Platform used
     * @access protected
     * @var string
     */
    protected $platform = 'library';

    /**
     * Platform version
     * @access protected
     * @var string
     */
    protected $platform_version = '2.0.1';

    /**
     * Module version
     * @access protected
     * @var string
     */
    protected $module_version = '1.1.5';

    /**
     * stores last API result
     * @access public
     * @var string
     */
    public $last_request = '';

    /**
     * Return language code
     * @access protected
     * @var string
     */
    protected $lang_code = 'en-US';

    /**
     * Class constructor.
     * @access public
     * @return Void
     */
    public function __construct()
    {
        $this->auth['user'] = EMC_USER;
        $this->auth['pass'] = EMC_PASS;
        $this->setEnv(EMC_MODE);
        $this->setLocale($this->lang_code);
        $this->param = array();
    }

    /**
     * Function which executes api request.
     *
     * If an error occurs, we close curl call and put error details in $this->errorText variable.
     * We distinguish two situations with 404 code returned in the response : <br>
     * &nbsp;&nbsp;1) The API sets 404 code for valid request which doesn't contain any result.
     * The type of response is application/xml.<br>
     * &nbsp;&nbsp;2) The server sets 404 code too. It does it for resources which don't exist
     * (like every 404 web page).
     * &nbsp;&nbsp;In this case the responses' type is text/html.<br>
     *
     * If the response returns 404 server code, we cancel the operation by setting $result to false,
     * $resp_error to true and by adding an error message to $resp_errors_list (with http_file_not_found value).
     *
     * In the case of 404 API error code, we don't break the operation. We show error messages in setResponseError().
     * @access public
     * @return String
     */
    public function doRequest()
    {
        $req = curl_init();
        foreach ($this->options as $key => $option) {
            curl_setopt($req, $key, $option);
        }
        $result = curl_exec($req);
        $this->last_request = $result;
        $curl_info = curl_getinfo($req);
        $this->curl_errno = curl_errno($req);
        $content_type = explode(';', $curl_info['content_type']);
        if (curl_errno($req) > 0 || curl_error($req)) {
            $this->curl_error = true;
            $this->curl_error_text = curl_error($req);
            $this->resp_errors_list[] = array('code' => 'curl_' . curl_errno($req),
                'url' => $curl_info['url'],
                'message' => curl_error($req)
              );
            curl_close($req);
            return false;
        } elseif ($curl_info['http_code'] != '200' && $curl_info['http_code'] != '400'
        && $curl_info['http_code'] != '401') {
            $result = false;
            $this->resp_error = true;
            $this->resp_errors_list[] = array('code' => 'http_error_' . $curl_info['http_code'],
                'url' => $curl_info['url'],
                'message' =>
                  'Echec lors de l\'envoi de la requête, le serveur n\'a pas pu répondre correctement (erreur :' .
                    $curl_info['http_code'] . ')');
        } elseif (trim($content_type[0]) != 'application/xml' && trim($content_type[0]) != 'application/x-download') {
            $result = false;
            $this->resp_error = true;
            $this->resp_errors_list[] = array('code' => 'bad_response_format',
                'url' => $curl_info['url'],
                'message' =>
                  'Echec lors de l\'envoi de la requête, le serveur a envoyé une réponse invalide ' .
                  '(format de la réponse : ' . $content_type[0] . ')');
        }
        curl_close($req);

        return $result;
    }

    /**
     * Function which executes api request with curl multi.
     *
     * If an error occurs, we close curl call and put error details in $this->errorText variable.
     * We distinguish two situations with 404 code returned in the response : <br>
     * &nbsp;&nbsp;1) The API sets 404 code for valid request which doesn't contain any result.
     * The type of response is application/xml.<br>
     * &nbsp;&nbsp;2) The server sets 404 code too. It does it for resources which don't exist
     * (like every 404 web page).
     * &nbsp;&nbsp;In this case the responses' type is text/html.<br>
     *
     * If the response returns 404 server code, we cancel the operation by setting $result to false,
     * $resp_error to true and by adding an error message to $resp_errors_list (with http_file_not_found value).
     *
     * In the case of 404 API error code, we don't break the operation. We show error messages in setResponseError().
     * @access public
     * @return String
     */
    public function doRequestMulti()
    {
        $data = array();
        $ch = array();
        $mh = curl_multi_init();
        $i = 0;

        foreach ($this->options as $u) {
            $ch[$i] = curl_init();
            curl_setopt($ch[$i], CURLOPT_SSL_VERIFYPEER, $u[CURLOPT_SSL_VERIFYPEER]);
            curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, $u[CURLOPT_RETURNTRANSFER]);
            curl_setopt($ch[$i], CURLOPT_URL, $u[CURLOPT_URL]);
            curl_setopt($ch[$i], CURLOPT_HTTPHEADER, $u[CURLOPT_HTTPHEADER]);
            curl_setopt($ch[$i], CURLOPT_CAINFO, $u[CURLOPT_CAINFO]);
            curl_setopt($ch[$i], CURLOPT_SSL_VERIFYPEER, $u[CURLOPT_SSL_VERIFYPEER]);
            curl_setopt($ch[$i], CURLOPT_TIMEOUT_MS, $this->timeout);

            curl_multi_add_handle($mh, $ch[$i]);
            $i++;
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);
        /*
        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
        */
        foreach ($ch as $k => $c) {
            $data[$k] = curl_multi_getcontent($c);
            curl_multi_remove_handle($mh, $c);
        }

        foreach ($ch as $k => $c) {
            $curl_info = curl_getinfo($c);
            $content_type = explode(';', $curl_info['content_type']);
            if (curl_errno($c) > 0 || curl_error($c)) {
                $this->curl_error = true;
                $this->curl_error_text = curl_error($c);
                $this->resp_errors_list[$k][] = array('code' => 'curl_' . curl_errno($c),
                    'url' => $curl_info['url'],
                    'message' => curl_error($c)
                  );
                $data[$k] = false;
            } elseif ($curl_info['http_code'] != '200' && $curl_info['http_code'] !=
              '400' && $curl_info['http_code'] != '401') {
                $this->resp_errors_list[$k][] = array('code' => 'http_error_' . $curl_info['http_code'],
                    'url' => $curl_info['url'],
                    'message' =>
                      'Echec lors de l\'envoi de la requête, le serveur n\'a pas pu répondre correctement (erreur :' .
                        $curl_info['http_code'] . ')');
            } elseif (trim($content_type[0]) != 'application/xml') {
                $this->resp_errors_list[$k][] = array('code' => 'bad_response_format',
                    'url' => $curl_info['url'],
                    'message' =>
                      'Echec lors de l\'envoi de la requête, le serveur a envoyé une réponse invalide ' .
                      '(format de la réponse : ' . $content_type[0] . ')');
            }
        }
        curl_multi_close($mh);

        return $data;
    }

    /**
     * Request options setter. If prod environment, sets Verisign's certificate.
     * @access public
     * @param Array $options The request options.
     * @return Void
     */
    public function setOptions($options)
    {
        $this->setSSLProtection();
        $this->options = array(
            CURLOPT_SSL_VERIFYPEER => $this->ssl_check['peer'],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYHOST => $this->ssl_check['host'],
            CURLOPT_URL => $this->server . $options['action'] . $this->get_params
        );

        $this->options[CURLOPT_HTTPHEADER] = array(
            'Accept-Language: '.$this->lang_code,
            'Api-Version: '.$this->api_version
        );

        if (!empty($this->auth['user']) && !empty($this->auth['pass'])) {
            array_push(
                $this->options[CURLOPT_HTTPHEADER],
                'Authorization: ' . base64_encode($this->auth['user'] . ':' . $this->auth['pass']) . ''
            );
        }
        if ($this->timeout != null) {
            $this->options[CURLOPT_TIMEOUT_MS] = $this->timeout;
        }

        $this->param['action'] = $options['action'];
    }

    /**
     * Request options setter for curl multi request. If prod environment, sets Verisign's certificate.
     * @access public
     * @param Array $options The request options.
     * @return Void
     */
    public function setOptionsMulti($options)
    {
        $this->setSSLProtection();
        foreach ($this->get_params as $param) {
            $this->options[] = array(
                CURLOPT_SSL_VERIFYPEER => $this->ssl_check['peer'],
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYHOST => $this->ssl_check['host'],
                CURLOPT_URL => $this->server . $options['action'] . $param,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . base64_encode($this->auth['user'] . ':' . $this->auth['pass']) . '',
                    'Accept-Language: '.$this->lang_code,
                    'Api-Version: '.$this->api_version)
            );
        }
    }

    /**
     * It determines if CURL has to check SSL connection or not.
     * @access private
     * @return Void
     */
    private function setSSLProtection()
    {
        if ($this->server != 'https://www.envoimoinscher.com/') {
            $this->ssl_check['peer'] = false;
            $this->ssl_check['host'] = 0;
        }
    }

    /**
     * Function which sets the post request.
     * @access public
     * @return Void
     */
    public function setPost()
    {
        $this->param['platform'] = $this->platform;
        $this->param['platform_version'] = $this->platform_version;
        $this->param['module_version'] = $this->module_version;
        $this->options[CURLOPT_POST] = true;
        $this->options[CURLOPT_POSTFIELDS] = http_build_query($this->param);
    }

    /**
     * Function sets the get params passed into the request.
     * @access public
     * @return Void
     */
    public function setGetParams()
    {
        $this->param['platform'] = $this->platform;
        $this->param['platform_version'] = $this->platform_version;
        $this->param['module_version'] = $this->module_version;
        $this->get_params = '?' . http_build_query($this->param);
    }

    /**
     * Function sets the get params passed into the request for curl multi request.
     * @access public
     * @return Void
     */
    public function setGetParamsMulti()
    {
        $this->param['platform'] = $this->platform;
        $this->param['platform_version'] = $this->platform_version;
        $this->param['module_version'] = $this->module_version;
        $this->get_params = array();
        foreach ($this->param_multi as $param) {
            $this->get_params[] = '?' . http_build_query($param);
        }
    }

    /**
     * Function parses api server response.
     *
     * First, it checks if the parsed response doesn't contain <error /> tag. If not, it does nothing.
     * Otherwise, it makes $resp_error parameter to true, parses the reponse and sets error messages to
     * $resp_errors_list array.
     * @access public
     * @param String $document The response returned by API. For use it like a XPath object, we have to
     * parse it with PHPs' DOMDocument class.
     * @return Void
     */
    public function parseResponse($document)
    {
        $dom_cl = new \DOMDocument();
        $dom_cl->loadXML($document);
        $this->xpath = new \DOMXPath($dom_cl);
        if ($this->hasErrors()) {
            $this->setResponseErrors();
        }
    }

    /**
     * Function parses api server response for curl multi request.
     *
     * First, it checks if the parsed response doesn't contain <error /> tag. If not, it does nothing.
     * Otherwise, it makes $resp_error parameter to true, parses the reponse and sets error messages to
     * $resp_errors_list array.
     * @access public
     * @param String $document The response returned by API. For use it like a XPath object, we have to
     * parse it with PHPs' DOMDocument class.
     * @return Void
     */
    public function parseResponseMulti($documents)
    {
        $i = 0;
        $this->xpath = array();
        $return_xml = new \DOMDocument('1.0', 'UTF-8');
        $return_wrapper = new \DOMElement('multirequest_wrapper');
        $return_xml->appendChild($return_wrapper);
        $this->last_request = $documents;
        
        foreach ($documents as $document) {
            if (!$document) {
                $this->xpath[$i] = false;
                continue;
            }
            $dom_cl = new \DOMDocument();
            $dom_cl->loadXML($document);
            $this->xpath[$i] = new \DOMXPath($dom_cl);
            $target_node = $dom_cl->documentElement;
            $return_wrapper->appendChild($return_xml->importNode($target_node, true));

            if ($this->hasErrorsMulti($this->xpath[$i])) {
                $errors = $this->xpath[$i]->evaluate('/error');
                foreach ($errors as $e => $error) {
                    $this->resp_errors_list[$i][$e] = array(
                        'code' => $this->xpath[$i]->evaluate('code', $error)->item(0)->nodeValue,
                        'message' => $this->xpath[$i]->evaluate('message', $error)->item(0)->nodeValue
                    );
                }
            }

            $i++;
        }
    }

    /**
     * Function detects if xml document has error tag.
     * @access private
     * @param object xml document (if curl multi request)
     * @return boolean true if xml document has error tag, false if it hasn't.
     */
    private function hasErrors($xpath = false)
    {
        $xpath = $xpath ? $xpath : $this->xpath;
        if ((int)$xpath->evaluate('count(/error)') > 0) {
            $this->resp_error = true;
            return true;
        }
        return false;
    }

    /**
     * Function detects if xml document has error tag for curl multi request.
     * @access private
     * @param object xml document (if curl multi request)
     * @return boolean true if xml document has error tag, false if it hasn't.
     */
    private function hasErrorsMulti($xpath = false)
    {
        $xpath = $xpath ? $xpath : $this->xpath;
        if ((int)$xpath->evaluate('count(/error)') > 0) {
            return true;
        }
        return false;
    }

    /**
     * Function sets error messages to $resp_errors_list.
     * @access private
     * @return boolean true if xml document has error tag, false if it hasn't.
     */
    private function setResponseErrors($xpath = false)
    {
        $xpath = $xpath ? $xpath : $this->xpath;
        $errors = $xpath->evaluate('/error');
        foreach ($errors as $e => $error) {
            $this->resp_errors_list[$e] = array('code' => $xpath->evaluate('code', $error)->item(0)->nodeValue
            , 'message' => $xpath->evaluate('message', $error)->item(0)->nodeValue);
        }
    }

    /**
     * Sets environment.
     * @access public
     * @param String $env Server's environment: test or prod.
     * @return Void
     */
    public function setEnv($env)
    {
        $envs = array(ENV_TEST, ENV_PRODUCTION);

        if (in_array(strtolower($env), $envs)) {
            $var = 'server_' . strtolower($env);
            $this->server = $this->$var;
            $doc_var = 'document_server_' . strtolower($env);
            $this->document_server = $this->$doc_var;

            //To manage multiple development environments, used only by boxtal IT Team
            if (defined('SERVER_TEST_DOC')) {
                $this->document_server = SERVER_TEST_DOC;
            }
            if (defined('SERVER_TEST')) {
                $this->server = SERVER_TEST;
            }
            if (defined('EMC_USER_TEST')) {
                $this->auth['user'] = EMC_USER_TEST;
            }
            if (defined('EMC_PASS_TEST')) {
                $this->auth['pass'] = EMC_PASS_TEST;
            }
        }
    }

    /**
     * Sets login
     * @access public
     * @param String $env Server's environment : test or prod .
     * @return Void
     */
    public function setLogin($login)
    {
        $this->auth['user'] = $login;
    }
    /**
     * Sets password
     * @access public
     * @param String $env Server's environment : test or prod .
     * @return Void
     */
    public function setPassword($pwd)
    {
        $this->auth['pass'] = $pwd;
    }

    /**
     * Sets locale.
     * @access public
     * @param String $lang_code language code. Ex: 'fr-FR', 'en-US'.
     * @return Void
     */
    public function setLocale($lang_code)
    {
        $this->lang_code = $lang_code;
    }

    public function setParam($param)
    {
        $this->param = $param;
    }

    public function setPlatformParams($platform, $platform_version, $module_version)
    {
        $this->platform = $platform;
        $this->platform_version = $platform_version;
        $this->module_version = $module_version;
    }
    public function getApiParam()
    {
        return array( 'URL called'=> $this->server.$this->param['action'],
            'Params'=> $this->param
        );
    }
}
