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
}
