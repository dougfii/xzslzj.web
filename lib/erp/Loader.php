<?php

class Loader
{
    protected static $dirs = array('core/', 'core/db/', 'base/', 'base/biz/', 'base/cls/','base/sms/', 'erp/', 'erp/mod/', 'interface/', 'third/ExcelReader/', 'third/PHPExcel/', 'third/PHPMailer/');

    public static function load($cls)
    {
        foreach (self::$dirs as $dir)
        {
            $path = BASE_LIB_PATH . $dir . $cls . '.php';
            if (file_exists($path))
            {
                require_once $path;
                if (class_exists($cls, false) || interface_exists($cls, false))
                    return;
            }
        }
    }

    public static function autoload($cls)
    {
        try
        {
            self::load($cls);
        }
        catch (Exception $e)
        {
        }
    }
}

if (function_exists('spl_autoload_register'))
{
    spl_autoload_register(array('Loader', 'autoload'));
    if (function_exists('__autoload'))
        spl_autoload_register('__autoload');
}
else
{

    function __autoload($cls)
    {
        Loader::autoload($cls);
    }
}
?>