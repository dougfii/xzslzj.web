<?php

//momo
class ProjectStateCls
{
    const INIT = 0;
    const BEGIN = 1;
    const APPROVE = 2;
    const DENY = 3;
    const ALLOW = 4;

    private static $map = array(//
        0 => '',//
        1 => '新文档',//
        2 => '审核中',//
        3 => '审退',//
        4 => '审过',//
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

    public static function IsNew($id)
    {
        return $id == ProjectStateCls::INIT || $id == ProjectStateCls::BEGIN || $id == ProjectStateCls::APPROVE;
    }

    public static function IsEdit($id)
    {
        return $id == ProjectStateCls::INIT || $id == ProjectStateCls::BEGIN || $id == ProjectStateCls::DENY;
    }

    public static function IsApprove($id)
    {
        return $id == ProjectStateCls::APPROVE;
    }

    public static function IsFinished($id)
    {
        return $id >= ProjectStateCls::ALLOW;
    }
}

?>