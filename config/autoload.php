<?php
use \Emc\Autoloader;

require_once('config.php');

//To manage multiple developement envirnments, used only by boxtal IT Team
@include_once('environment.php');

require(__DIR__.'/../Emc/Autoloader.php');
Autoloader::register();
