<?php

//momo
class CalendarBiz
{
    public static function Item($id)
    {
        $rs = CalendarCls::Instance()->Item($id);
        return Data::FormatSuccess(count($rs), $rs);
    }

    public static function Items($uid, $res, $where, $order, $page, $size, $self = true)
    {
        $where = 'WHERE del=false ' . $where;
        $order = empty($order) ? 'ORDER BY id DESC' : $order;

        $where .= Common::GetTablePowerSql($uid, $res, $self);

        $count = CalendarCls::Count($where);
        $page = HTML::PageNum($count, $page, $size);
        $start = HTML::PagePos($count, $page, $size);
        $rs = CalendarCls::Items($where, $order, $start, $size);

        return Data::FormatSuccess($count, $rs);
    }

    public static function Add($date, $stateid, $customer, $contacts, $content, $plan, $walkway, $distance, $memo, $uid)
    {
        if (!Date::IsDatetime($date))
            return Data::FormatError('请选择正确的工作日期');
        if (empty ($stateid))
            return Data::FormatError('请选择日程状态');
        if (empty($customer))
            return Data::FormatError('请输入客户');
        if (!Util::IsMaxLen($customer, 200))
            return Data::FormatError('客户过长');
        if (empty($contacts))
            return Data::FormatError('请输入联系人');
        if (!Util::IsMaxLen($contacts, 200))
            return Data::FormatError('联系人过长');
        if (Util::isBlank($content))
            return Data::FormatError('请输入谈何事');
        if (!Util::IsMaxLen($walkway, 200))
            return Data::FormatError('路线过长');
        if (!Util::IsMaxLen($distance, 200))
            return Data::FormatError('里程过长');

        $date = Date::Format($date, 'd', null);

        try
        {
            DB::db()->BeginTransaction();

            $id = CalendarCls::Add($date, $stateid, $customer, $contacts, $content, $plan, $walkway, $distance, $memo, $uid);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        }
        catch (Exception $e)
        {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Edit($id, $date, $stateid, $customer, $contacts, $content, $plan, $walkway, $distance, $memo, $uid)
    {
        if ($id <= 0)
            return Data::FormatError(ALERT_ERROR);
        if (!Date::IsDatetime($date))
            return Data::FormatError('请选择正确的工作日期');
        if (empty ($stateid))
            return Data::FormatError('请选择日程状态');
        if (empty($customer))
            return Data::FormatError('请输入客户');
        if (!Util::IsMaxLen($customer, 200))
            return Data::FormatError('客户过长');
        if (empty($contacts))
            return Data::FormatError('请输入联系人');
        if (!Util::IsMaxLen($contacts, 200))
            return Data::FormatError('联系人过长');
        if (Util::isBlank($content))
            return Data::FormatError('请输入谈何事');
        if (!Util::IsMaxLen($walkway, 200))
            return Data::FormatError('路线过长');
        if (!Util::IsMaxLen($distance, 200))
            return Data::FormatError('里程过长');

        if (!CalendarCls::Instance()->IsEdit($id, $uid))
            return Data::FormatError('当前状态不可编辑或非创建者');

        $date = Date::Format($date, 'd', null);

        try
        {
            DB::db()->BeginTransaction();

            CalendarCls::Edit($id, $date, $stateid, $customer, $contacts, $content, $plan, $walkway, $distance, $memo);

            DB::db()->Commit();

            return Data::FormatSuccess();
        }
        catch (Exception $e)
        {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Delete($id)
    {
        try
        {
            DB::db()->BeginTransaction();

            CalendarCls::Delete($id);

            DB::db()->Commit();

            return Data::FormatSuccess();
        }
        catch (Exception $e)
        {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }
}

?>