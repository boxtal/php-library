<?php
namespace Emc;

/**
* 2011-2016 Boxtale
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
* @author    Boxtale EnvoiMoinsCher <informationapi@boxtale.com>
* @copyright 2011-2016 Boxtale
* @license   http://www.gnu.org/licenses/
*/

/**
 * Used to ship products from multiple warehouses
 */
class QuotationMulti extends Quotation
{

   /**
    * [__construct description]
    * @param [Array] $multirequest    indexed array containing quotation information, namely "from", "to", "parcels" and "additional_params"
    */
    public function __construct($multirequest)
    {
        parent::__construct();

        foreach ($multirequest as $quot_index => $quot_info) {
            // set additional params
            $params = $quot_info['additional_params'];

            // Set sender
            foreach ($quot_info['from'] as $key => $value) {
                $params['expediteur.' . $key] = $value;
            }

            // Set recipient
            foreach ($quot_info['to'] as $key => $value) {
                $params['destinataire.' . $key] = $value;
            }

            // Set parcel
            foreach ($quot_info['parcels']['dimensions'] as $d => $data) {
                $params[$quot_info['parcels']['type'] . '_' . $d . '.poids'] = $data['poids'];
                $params[$quot_info['parcels']['type'] . '_' . $d . '.longueur'] = $data['longueur'];
                $params[$quot_info['parcels']['type'] . '_' . $d . '.largeur'] = $data['largeur'];
                $params[$quot_info['parcels']['type'] . '_' . $d . '.hauteur'] = $data['hauteur'];
            }

            $this->setParamMulti($params);
        }
    }
}
