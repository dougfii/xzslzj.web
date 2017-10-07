<?php

class ReplyBiz
{
    public static function Item($id)
    {
        return Data::FormatSuccess(1, ReplyCls::Instance()->Item($id));
    }

    public static function Xxx($id)
    {
        return Data::FormatSuccess(ReplyCls::Instance()->Xxx($id));
    }

    public static function Items($where, $order, $page, $size)
    {
        $where = 'WHERE del=false ' . $where;
        $order = empty($order) ? 'ORDER BY id DESC' : $order;

        $count = ReplyCls::Count($where);
        $page = HTML::PageNum($count, $page, $size);
        $start = HTML::PagePos($count, $page, $size);
        $rs = ReplyCls::Items($where, $order, $start, $size);

        return Data::FormatSuccess($count, $rs);
    }

    public static function Deny($mid, $fid, $nodeid, $content, $uid)
    {
        if ($mid <= 0 || $fid <= 0 || $nodeid <= 0 || $uid <= 0) return Data::FormatError(ALERT_ERROR);
        if (Util::isBlank($content)) return Data::FormatError('请输入审批意见');

        $act = 0;
        $stateid = ProjectStateCls::DENY;

        try {
            DB::db()->BeginTransaction();

            $id = ReplyCls::Add($mid, $fid, $nodeid, '', $content, '', $uid, $act);
            FlowCls::SetReply($mid, $nodeid, $stateid, $uid);
            MemberCls::SetNodeState($mid, $nodeid, $stateid);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Allow1($mid, $fid, $nodeid, $no, $content, $date, $uid)
    {
        if ($mid <= 0 || $fid <= 0 || $nodeid <= 0 || $uid <= 0) return Data::FormatError(ALERT_ERROR);
        if (Util::isBlank($no)) return Data::FormatError('请输入文件编号');
        if (Util::isBlank($content)) return Data::FormatError('请输入批复内容');
        if (Util::isBlank($date)) return Data::FormatError('请输入批复日期');

        $act = 1;
        $stateid = ProjectStateCls::ALLOW;

        try {
            DB::db()->BeginTransaction();

            $id = ReplyCls::Add($mid, $fid, $nodeid, $no, $content, $date, $uid, $act);
            FlowCls::SetReply($mid, $nodeid, $stateid, $uid);
            MemberCls::SetNodeState($mid, $nodeid, $stateid);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function GetReplyItem($mid, $fid, $nodeid)
    {
        $rs = ReplyCls::GetReplyItem($mid, $fid, $nodeid);
        return array(count($rs), $rs);
    }
}

?>