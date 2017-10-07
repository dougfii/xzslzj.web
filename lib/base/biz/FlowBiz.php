<?php

class FlowBiz
{
    public static function Item($id)
    {
        return array(1, FlowCls::Instance()->Item($id));
    }

    public static function Mid($id)
    {
        return FlowCls::Instance()->Mid($id);
    }

    public static function Items($where, $order, $page, $size)
    {
        $where = 'WHERE del=false ' . $where;
        $order = empty($order) ? 'ORDER BY id DESC' : $order;

        $count = FlowCls::Count($where);
        $page = HTML::PageNum($count, $page, $size);
        $start = HTML::PagePos($count, $page, $size);
        $rs = FlowCls::Items($where, $order, $start, $size);

        return array($count, $rs);
    }

    public static function Add1($mid, $nodeid, $stateid, $no, $signer, $content, $date, $keywords, $attachments)
    {
        if ($mid <= 0 || $nodeid <= 0 || $stateid <= 0) return Data::FormatError(ALERT_ERROR);
        if (Util::isBlank($no)) return Data::FormatError('请输入文件编号');
        if (Util::isBlank($signer)) return Data::FormatError('请输入签发单位');
        if (Util::isBlank($content)) return Data::FormatError('请输入申报内容');
        if (Util::isBlank($date)) return Data::FormatError('请输入申报日期');

        // if (FlowCls::ExistNodeId($mid, $nodeid)) return Data::FormatError('申报已经存在');

        try {
            DB::db()->BeginTransaction();

            $id = FlowCls::Add1($mid, $nodeid, $stateid, $no, $signer, $content, $date, $keywords, $attachments);
            MemberCls::SetNodeState($mid, $nodeid, $stateid);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Add3($mid, $nodeid, $stateid, $no, $signer, $content, $date, $attachments, $comp, $date_ping, $items, $total1, $total2, $total3, $total4, $m11, $m12, $m13, $m21, $m22, $m23, $m31, $m32, $m33, $m41, $m42, $m43, $m51, $m52, $m53, $m61, $m62, $m63)
    {
        if ($mid <= 0 || $nodeid <= 0 || $stateid <= 0) return Data::FormatError(ALERT_ERROR);
        if (empty($comp)) return Data::FormatError('请输入施工单位');
        if (empty($no)) return Data::FormatError('请输入工程编号');
        if (empty($date_ping)) return Data::FormatError('请输入评定日期');

        //if (empty($items)) return Data::FormatError('请设置评定项目');

        if (empty($total1)) return Data::FormatError('请输入应得分');
        if (empty($total2)) return Data::FormatError('请输入实得分');
        if (empty($total3)) return Data::FormatError('请输入得分率');
        if (empty($total4)) return Data::FormatError('请输入外观质量等级');

        if (empty($m11)) return Data::FormatError('请输入项目法人单位名称');
        if (empty($m12)) return Data::FormatError('请输入项目法人职称');
        if (empty($m13)) return Data::FormatError('请输入项目法人签名');

        if (empty($m21)) return Data::FormatError('请输入监理单位单位名称');
        if (empty($m22)) return Data::FormatError('请输入监理单位职称');
        if (empty($m23)) return Data::FormatError('请输入监理单位签名');

        if (empty($m31)) return Data::FormatError('请输入设计单位单位名称');
        if (empty($m32)) return Data::FormatError('请输入设计单位职称');
        if (empty($m33)) return Data::FormatError('请输入设计单位签名');

        if (empty($m41)) return Data::FormatError('请输入施工单位单位名称');
        if (empty($m42)) return Data::FormatError('请输入施工单位职称');
        if (empty($m43)) return Data::FormatError('请输入施工单位签名');

        if (empty($m51)) return Data::FormatError('请输入检测单位单位名称');
        if (empty($m52)) return Data::FormatError('请输入检测单位职称');
        if (empty($m53)) return Data::FormatError('请输入检测单位签名');

        if (empty($m61)) return Data::FormatError('请输入运行管理单位单位名称');
        if (empty($m62)) return Data::FormatError('请输入运行管理单位职称');
        if (empty($m63)) return Data::FormatError('请输入运行管理单位签名');

        if (empty($content)) return Data::FormatError('请输入核定意见');
        if (empty($signer)) return Data::FormatError('请输入核定人');
        if (empty($date)) return Data::FormatError('请输入日期');

        try {
            DB::db()->BeginTransaction();

            $id = FlowCls::Add3($mid, $nodeid, $stateid, $no, $signer, $content, $date, $attachments, $comp, $date_ping, $items, $total1, $total2, $total3, $total4, $m11, $m12, $m13, $m21, $m22, $m23, $m31, $m32, $m33, $m41, $m42, $m43, $m51, $m52, $m53, $m61, $m62, $m63);
            MemberCls::SetNodeState($mid, $nodeid, $stateid);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Delete($id)
    {
        if ($id <= 0) return Data::FormatError(ALERT_ERROR);

        try {
            DB::db()->BeginTransaction();

            $id = FlowCls::Delete($id);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function SetAct($id)
    {
        if ($id <= 0) return Data::FormatError(ALERT_ERROR);

        try {
            DB::db()->BeginTransaction();

            $id = FlowCls::SetAct($id);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function GetFlowItem($mid, $nodeid)
    {
        $rs = FlowCls::GetFlowItem($mid, $nodeid);
        return array(count($rs), $rs);
    }
}

?>