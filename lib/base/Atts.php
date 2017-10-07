<?php

class Atts
{
    public static $flow1 = array('质量监督申报表', '工程项目初步设计批复和项目法人单位文件批复', '项目法人与设计,监理,施工单位签订的合同', '项目法人第三方委托检测计划', '其他一', '其他二');
    public static $flow2 = array('编制说明', '项目划分表', '其他');
    public static $flow51 = array('重要隐蔽（关键部位）单元工程质量等级签证', '重要隐蔽（关键部位）单元工程质量评定表');
    public static $flow52 = array('重要隐蔽（关键部位）单元工程质量等级核备表', '分部工程质量评定表', '分部工程验收鉴定书');
    public static $flow61 = array('重要隐蔽（关键部位）单元工程质量等级核备表', '分部工程质量评定表', '分部工程验收鉴定书');
    public static $flow63 = array('单位工程质量评定表', '工程外观质量评定表', '单位工程验收鉴定书');
    public static $flow64 = array('项目法人质量核定申请', '质量核定申报书', '项目法人委托检测报告', '工程项目质量评定表');
    public static $flow71 = array();
    public static $flow72 = array();
    public static $flow73 = array();
    public static $flow74 = array();
    public static $flow75 = array();
    public static $flow8 = array();
    public static $flow91 = array();
    public static $flow921 = array('项目法人委托检测报告', '工程项目施工质量评定表（竣工验收阶段提供）');
    public static $flow922 = array('项目法人委托检测报告', '工程项目施工质量评定表（竣工验收阶段提供）');
    public static $flow923 = array('项目法人委托检测报告', '工程项目施工质量评定表（竣工验收阶段提供）');
    public static $flow924 = array('项目法人委托检测报告', '工程项目施工质量评定表（竣工验收阶段提供）');
    public static $flow925 = array('项目法人委托检测报告', '工程项目施工质量评定表（竣工验收阶段提供）');
    public static $flow9242 = array('工程项目质量监督人员情况表', '工程建设过程中质量监督检查意见汇总表');
    public static $flow9252 = array('工程项目质量监督人员情况表', '工程建设过程中质量监督检查意见汇总表');


    public static function UploadFixed($atts, $rs, $edit = false, $title = true, $admin = false)
    {
        $a = array();
        if (!empty($atts) && is_array($atts)) {
            foreach ($atts as $k => $v) {
                $a[$k + 1] = array('name' => $v, 'url' => '', 'ext' => '', 'id' => '');
            }
        }

        if (!empty($rs) && is_array($rs)) {
            foreach ($rs as $k => $v) {
                if (isset($a[$v['no']])) {
                    $a[$v['no']]['url'] = $v['url'];
                    $a[$v['no']]['ext'] = $v['ext'];
                    $a[$v['no']]['id'] = $v['id'];
                }
            }
        }

        $s = '<div>';
        if ($title) $s .= '<div class="att">附件:</div>';
        $s .= '<div class="atts">';
        $s .= '<ul>';
        foreach ($a as $k => $v) {
            $s .= '<li>' . $k . '. <span id="atta' . $k . '">';
            if (!empty($v['name']) && !empty($v['url'])) {
                $s .= '<a href="' . $v['url'] . '" target="_blank">' . $v['name'] . '</a>';
            } else {
                $s .= $v['name'];
            }
            $s .= '</span>';
            if ($edit) $s .= '　　<span class="up up' . $k . '">上传<input type="file" id="upfile' . $k . '" class="upfile" fid="' . $k . '" fname="' . $v['name'] . '" name="upfile' . $k . '" /></span>';
            if ($admin && !empty($v['name']) && !empty($v['url'])) $s .= '　　<a href="javascript:;" class="delattr" did="' . $v['id'] . '">[管理删除]</a>';
            $s .= '</li>';
        }
        $s .= '</ul>';
        $s .= '</div></div>';

        return $s;
    }

    public static function UploadDynamic($rs, $edit = false, $title = true, $admin = false)
    {
        $s = '<div>';
        if ($title) $s .= '<div class="att">附件:</div>';
        $s .= '<div class="atts">';
        $s .= '<ul>';
        foreach ($rs as $k => $v) {
            $s .= '<li id="atta' . $v['id'] . '">';
            if (!empty($v['name']) && !empty($v['url'])) {
                $s .= '<a href="' . $v['url'] . '" target="_blank">' . $v['name'] . '</a>';
            } else {
                $s .= $v['name'];
            }
            if ($edit) $s .= '　　<a href="javascript:;" class="upd" did="' . $v['id'] . '">删除</a>';
            if ($admin && !empty($v['name']) && !empty($v['url'])) $s .= '　　<a href="javascript:;" class="delattr" did="' . $v['id'] . '">[管理删除]</a>';

            $s .= '</li>';
        }
        $s .= '</ul>';
        $s .= '</div>';
        if ($edit) $s .= '<div id="attb"><span class="up">添加上传<input type="file" id="upfile" class="upfile" name="upfile" /></span></div>';
        $s .= '</div>';

        return $s;
    }
}

?>