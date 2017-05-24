<?php
/**
* 2011-2017 Boxtal
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
* @copyright 2011-2017 Boxtal
* @license   http://www.gnu.org/licenses/
*/

namespace Emc;

class User extends WebService
{

    /**
     * Array with user configuration informations.
     * @access public
     * @var array
     */
    public $user_configuration = array();

    /**
     * String with user partnership code.
     * @access public
     * @var string
     */
    public $partnership = "";

    /**
     * Gets information about partnership for logged user.
     * @access public
     * @return Void
     */
    public function getPartnership()
    {
        $this->setOptions(array('action' => 'api/v1/partnership'));
        $this->setPartnership();
    }

    /**
     * Gets information about e-mail configuration for logged user.
     * This function is rendered useless by getUserDetails function
     * @access public
     * @return Void
     */
    public function getEmailConfiguration()
    {
        $this->setOptions(array('action' => 'api/v1/emails_configuration'));
        $this->setEmailConfiguration();
    }

    /**
     * Posts new informations about e-mail configuration for logged user.
     * Accepted keys are : label, notification, bill. If you want to remove the e-mail sending
     * for one of these keys, you must put into it an empty string like "".
     * @access public
     * @param Array $params Params with new e-mail configuration
     * @return Void
     */
    public function postEmailConfiguration($params)
    {
        $this->setOptions(array('action' => 'api/v1/emails_configuration'));
        $this->param = $params;
        $this->setPost();
        $this->setEmailConfiguration();
    }

    /**
     * Parses API response and puts the values into user configuration array.
     * @access private
     * @return Void
     */
    private function setEmailConfiguration()
    {
        $source = parent::doRequest();
        if ($source !== false) {
            parent::parseResponse($source);
            $mails = $this->xpath->evaluate('/user/mails')->item(0);
            if ($mails) {
                foreach ($mails->childNodes as $config_line) {
                    if (!($config_line instanceof DOMText)) {
                        $this->user_configuration['emails'][$config_line->nodeName] = $config_line->nodeValue;
                    }
                }
            }
        }
    }

    /**
     * Post request on api/v1/user_signup to create a user account
     * @access public
     * @param Array $params Params
     * @return String
     */
    public function postUserSignup($params)
    {
        $this->setOptions(array('action' => 'api/v1/user_signup'));
        $this->param = array_merge($this->param, $params);
        $this->setPost();
        $source = parent::doRequest();
        if ($source !== false) {
            parent::parseResponse($source);
            // The request is ok, we return trimed response
            $nodes = $this->xpath->query('/user/response');
            if (isset($nodes->item(0)->nodeValue)) {
                return trim($nodes->item(0)->nodeValue);
            } else {
                return trim($nodes->item);
            }
        } else {
            return $this->resp_error;
        }
    }

    /**
     * Gets information about user from server.
     * @access public
     * @return user_configuration
     */
    public function getUserDetails()
    {
        $this->setOptions(array('action' => 'api/v1/user_details'));
        $this->setUserDetails();
        return $this->user_configuration;
    }

    /**
     * Parses API response and puts the values into user configuration array.
     * @access private
     * @return Void
     */
    private function setUserDetails()
    {
        $source = parent::doRequest();
        if ($source !== false) {
            parent::parseResponse($source);
            $user = $this->xpath->query('/user')->item(0);
            if ($user) {
                foreach ($user->childNodes as $config_line) {
                    if (!($config_line instanceof DOMText)) {
                        if ($config_line->hasChildNodes() && $config_line->childNodes->length > 1) {
                            foreach ($config_line->childNodes as $sub_config_line) {
                                if (!($sub_config_line instanceof DOMText)) {
                                    $this->user_configuration[$config_line->nodeName][$sub_config_line->nodeName]
                                        = $sub_config_line->nodeValue;
                                }
                            }
                        } else {
                            $this->user_configuration[$config_line->nodeName] = $config_line->nodeValue;
                        }
                    }
                }
            }
        }
    }

    /**
     * Parses API response and puts the values into partnership attribute.
     * @access private
     * @return Void
     */
    private function setPartnership()
    {
        $source = parent::doRequest();
        if ($source !== false) {
            parent::parseResponse($source);
            $node = $this->xpath->evaluate('/user/partnership');
            if ($node && $node->item(0)) {
                $this->partnership = $node->item(0)->nodeValue;
            } else {
                $this->partnership = null;
            }
        }
    }

    /**
     * Post request on api/v1/user_keys to generate API keys
     * @access public
     * @param Array $params Params ('user.login' => "login")
     * @return String
     */
    public function postUserKeys($params)
    {
        $this->setOptions(array('action' => 'api/v1/user_keys'));
        $this->param = array_merge($this->param, $params);
        $this->setPost();
        $source = parent::doRequest();
        if ($source !== false) {
            parent::parseResponse($source);
            // The request is ok, we return trimed response
            $nodes = $this->xpath->query('/user/response');
            if (isset($nodes->item(0)->nodeValue)) {
                return trim($nodes->item(0)->nodeValue);
            } else {
                return trim($nodes->item);
            }
        } else {
            return $this->resp_error;
        }
    }
}
