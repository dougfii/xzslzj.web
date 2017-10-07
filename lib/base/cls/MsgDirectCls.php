<?php

//momo
class MsgDirectCls
{
    const FROM_USER = 1;
    const FROM_PROJECT = 2;

    private static $map = array(//
        1 => '从用户',//
        2 => '从项目',//
    );

    public static function Name($id)
    {
        if (array_key_exists($id, self::$map)) {
            return self::$map [$id];
        }
        return '';
    }

    public static function Count()
    {
        return count(self::$map);
    }

    public static function Items()
    {
        return self::$map;
    }
}

?>