<?php

class WordsFilter
{
    // 词语识别级别
    const UNKNOWN = 0; // 不清楚
    const NORMAL = 1; // 好的，正常的
    const WARN = 2; // 有风险的
    const DANGER = 3; // 危险的

    public static function Check($dict, $content)
    {
        $reg = '/' . implode('|', $dict) . '/i';
        return preg_match($reg, $content, $ret) ? $ret [0] : 0;
    }

    public static function Replace($dict, $content, $flag = '*')
    {
        $ws = array_combine($dict, array_fill(0, count($dict), $flag));
        return strtr($content, $ws);
    }
}