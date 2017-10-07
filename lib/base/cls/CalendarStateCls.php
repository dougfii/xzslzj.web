<?php

//momo
class CalendarStateCls
{
    const NEWADD = 1;
    const PROGRESS = 2;
    const FINISHED = 3;

    private static $map = array(//
        1 => '新计划',//
        2 => '进行中',//
        3 => '已完成'//
    );

    public static function Name($id)
    {
        if (array_key_exists($id, self::$map))
        {
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


    public static function IsEdit($id)
    {
        return $id < self::FINISHED;
    }
}

?>