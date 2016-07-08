<?php
namespace Emc;

/**
* ENv autoloader
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
        $class = str_replace('QuotationMulti', 'Quotation', $class);
        require $class .'.php';
    }
}
