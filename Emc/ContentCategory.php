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

class ContentCategory extends WebService
{

    /**
     * Public variable with categories array. The categories ids are the array keys.
     * <samp>
     * Structure :<br>
     * $categories[code]    => array(<br>
     * &nbsp;&nbsp;['label']                        => data<br>
     * &nbsp;&nbsp;['code']                        => data<br>
     *    )
     * </samp>
     * @access public
     * @var array
     */
    public $categories = array();

    /**
     * Public variable with contents array. Every content element is attached to one category.
     * <samp>
     * Structure :<br>
     * $contents[category][x]    => array(<br>
     * &nbsp;&nbsp;['code']                                => data<br>
     * &nbsp;&nbsp;['label']                                => data<br>
     * &nbsp;&nbsp;['category']                        => data<br>
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
        $this->setOptions(array('action' => 'api/v1/content_categories'));
        $this->doCatRequest();
    }

    /**
     * Function loads all contents.
     * @access public
     * @return String
     */
    public function getContents()
    {
        $this->setOptions(array('action' => 'api/v1/contents'));
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
        if ($source !== false) {
            parent::parseResponse($source);
            if (count($this->resp_errors_list) == 0) {
                /* The XML file is loaded, we now gather the datas */
                $categories = $this->xpath->query('/content_categories/content_category');
                foreach ($categories as $category) {
                    $code = $this->xpath->query('./code', $category)->item(0)->nodeValue;
                    $translationNodes = $this->xpath->query('./translations/translation', $category);
                    $translations = array();
                    foreach ($translationNodes as $translationNode) {
                        $translations[] = array(
                            'locale' => $this->xpath->query('./locale', $translationNode)->item(0)->nodeValue,
                            'label' => $this->xpath->query('./label', $translationNode)->item(0)->nodeValue
                        );
                    }

                    $this->categories[$code] = array(
                        'label' => $this->xpath->evaluate('./label', $category)->item(0)->nodeValue,
                        'code' => $code,
                        'translations' => $translations
                    );
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
        if ($source !== false) {
            parent::parseResponse($source);
            if (count($this->resp_errors_list) == 0) {
                /* The XML file is loaded, we now gather the datas */
                $contents = $this->xpath->query('/contents/content');
                foreach ($contents as $content) {
                    $category_id = $this->xpath->query('./category', $content)->item(0)->nodeValue;
                    if (!isset($this->contents[$category_id])) {
                        $this->contents[$category_id] = array();
                    }
                    $translationNodes = $this->xpath->query('./translations/translation', $content);
                    $translations = array();
                    foreach ($translationNodes as $translationNode) {
                        $translations[] = array(
                            'locale' => $this->xpath->query('./locale', $translationNode)->item(0)->nodeValue,
                            'label' => $this->xpath->query('./label', $translationNode)->item(0)->nodeValue
                        );
                    }

                    array_push($this->contents[$category_id], array(
                        'code' => $this->xpath->query('./code', $content)->item(0)->nodeValue,
                        'label' => $this->xpath->query('./label', $content)->item(0)->nodeValue,
                        'category' => $category_id,
                        'prohibited' => $this->xpath->query('./prohibited', $content)->item(0)->nodeValue === 'true',
                        'translations' => $translations
                      ));
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
