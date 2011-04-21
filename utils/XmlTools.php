<?php
// utils en stand by for now
class Utils_XmlTools {

  public function fromXmlToArray($source) {
    $domCl = new DOMDocument(); 
    $domCl->loadXML($source);
  }

}

?>