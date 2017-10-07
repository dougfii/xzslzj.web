<?php

//momo
class ProjectNodeCls
{
    const INIT = 0;
    const APPLY = 1;
    const DIVIDE = 2;
    const CONFIRM = 3;
    const SUGGEST = 4;
    const MATERIAL = 5;
    const CHECK = 6;
    const RECORD = 7;
    const PROGRESS = 8;
    const ACCEPT = 9;

    //sub
    const MATERIAL_1 = 51;
    const MATERIAL_2 = 52;
    const CHECK_1 = 61;
    const CHECK_2 = 62;
    const CHECK_3 = 63;
    const CHECK_4 = 64;
    const RECORD_1 = 71;
    const RECORD_2 = 72;
    const RECORD_3 = 73;
    const RECORD_4 = 74;
    const RECORD_5 = 75;
    const RECORD_6 = 76;
    const ACCEPT_1 = 91;
    const ACCEPT_2 = 92;
    const ACCEPT_21 = 921;
    const ACCEPT_22 = 922;
    const ACCEPT_23 = 923;
    const ACCEPT_24 = 924;
    const ACCEPT_25 = 925;


    const INSPECT = 9999;

    private static $map = array(//
        0 => '工程立项',//
        1 => '质量监督申报',//
        2 => '单元工程质量项目划分',//
        3 => '外观质量检查与评定项目确认',//
        4 => '质量监督检查意见表',//
        5 => '工程核备资料',//
        6 => '工程核定资料',//
        7 => '工程备案资料',//
        8 => '工程进度结点申报',//
        9 => '工程验收' //
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

    private static $material = array(//
        51 => '重要隐蔽（关键部位）单元工程核备',//
        52 => '阶段验收分部工程质量核备',//
    );

    public static function MaterialItems()
    {
        return self::$material;
    }

    public static function MaterialName($id)
    {
        if (array_key_exists($id, self::$material)) {
            return self::$material [$id];
        }
        return '';
    }

    private static $check = array(//
        61 => '大型枢纽分部验收结论',//
        62 => '单位工程外观质量结论',//
        63 => '单位工程质量等级核定表',//
        64 => '工程项目质量等级核定表',//
    );

    public static function CheckItems()
    {
        return self::$check;
    }

    public static function CheckName($id)
    {
        if (array_key_exists($id, self::$check)) {
            return self::$check [$id];
        }
        return '';
    }

    private static $record = array(//
        71 => '项目划分调整',//
        72 => '地基验槽签证',//
        73 => '质量缺陷备案',//
        74 => '工程质量事故处理结果',//
        75 => '合同签定书',//
        76 => '单位工程验收鉴定书',//
    );

    public static function RecordItems()
    {
        return self::$record;
    }

    public static function RecordName($id)
    {
        if (array_key_exists($id, self::$record)) {
            return self::$record [$id];
        }
        return '';
    }

    private static $accept = array(//
        91 => '法人验收',//
        92 => '政府验收',//
    );

    public static function AcceptItems()
    {
        return self::$accept;
    }

    public static function AcceptName($id)
    {
        if (array_key_exists($id, self::$accept)) {
            return self::$accept [$id];
        }
        return '';
    }

    private static $accept2 = array(//
        921 => '阶段验收（水下工程）',//
        922 => '部分工程投入验收',//
        923 => '泵站机组启动验收',//
        924 => '竣工预验收',//
        925 => '竣工验收',//
    );

    public static function Accept2Items()
    {
        return self::$accept2;
    }

    public static function Accept2Name($id)
    {
        if (array_key_exists($id, self::$accept2)) {
            return self::$accept2 [$id];
        }
        return '';
    }
}

?>