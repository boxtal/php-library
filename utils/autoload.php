<?php
// fonction qui auto-charge les classes
function autoload($className) {
  $path = explode("_", $className);
  $last = count($path) - 1;
  $className = $path[$last];
  unset($path[$last]);
  $classPath = implode("/", $path);
  require_once("../".strtolower($classPath)."/".$className.".php");
} 
spl_autoload_register('autoload'); 
?>