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

/**
* Env autoloader
*/
class Autoloader
{
    public static function register()
    {
        spl_autoload_register(array(__CLASS__,'autoload'));
    }

    public static function autoload($class)
    {
        $class = str_replace('Emc\\', '', $class);
        $class = str_replace('\\', '/', $class);
        $class = str_replace('Env', '', $class);
        require $class .'.php';
    }
}
