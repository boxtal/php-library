<?php
// stand by for now 
class Utils_FormatTools {

  public static function toArray($from, $source) {
    $class = "Utils_".$from."Tools";
    $inst = new $class;
    return $inst->{"from".$from."ToArray"}($source);
  }

  public static function prepareSelect($list) {
    $newList = array();
    foreach($list as $n => $node) {
      if($node["category"] == "") {
        $newList[$node['code']] = array("label" => $node['label'],
        "children" => array());
      }
      else {
        $index = count($newList[$node["category"]]["children"]);
        $newList[$node["category"]]["children"][$index] = array("label" => $node['label'], 
        "code" => $node["code"]);
      }
    }
    return $newList;
  }

}

?>