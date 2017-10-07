<?php

class ArticleBiz
{
    public static function Item($id)
    {
        return Data::FormatSuccess(1, ArticleCls::Instance()->Item($id));
    }

    public static function Name($id)
    {
        return Data::FormatSuccess(ArticleCls::Instance()->Name($id));
    }

    public static function Pid($id)
    {
        return Data::FormatSuccess(ArticleCls::Instance()->Pid($id));
    }

    public static function Items($where, $order, $page, $size)
    {
        $where = 'WHERE del=false ' . $where;
        $order = empty($order) ? 'ORDER BY id DESC' : $order;

        $count = ArticleCls::Count($where);
        $page = HTML::PageNum($count, $page, $size);
        $start = HTML::PagePos($count, $page, $size);
        $rs = ArticleCls::Items($where, $order, $start, $size);

        return Data::FormatSuccess($count, $rs);
    }

    public static function GetParentList($id, $self = true, $root = false)
    {
        $ret = array();
        $rs = ArticleCls::GetParents($id, $self, $root);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $ret[$v] = ArticleCls::Instance()->Name($v);
            }
        }
        return Data::FormatSuccess(count($ret), $ret);
    }

    public static function GetNextList($id, $self = false)
    {
        $ret = array();
        $rs = ArticleCls::GetNextIds($id, $self);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $ret[$v] = ArticleCls::Instance()->Name($v);
            }
        }
        return Data::FormatSuccess(count($ret), $ret);
    }

    public static function Add($tid, $uid, $pic, $title, $content, $html, $available, $roll, $act, $time)
    {
        if ($uid <= 0) return Data::FormatError(ALERT_ERROR);
        if ($tid <= 0) return Data::FormatError('请选择分类');
        if (Util::isBlank($title)) return Data::FormatError('请输入标题');
        if (!Util::IsMaxLen($title, 200)) return Data::FormatError('标题过长');
        if (!$available) return Data::FormatError('请输入发表内容');

        if ($tid == 1) return Data::FormatError('当前分类不允许发表文章');

        try {
            DB::db()->BeginTransaction();

            $id = ArticleCls::Add($tid, $uid, $pic, $title, $content, $html, $roll, $act, $time);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Edit($id, $tid, $uid, $pic, $title, $content, $html, $available, $roll, $act, $time)
    {
        if ($id <= 0 || $uid <= 0) return Data::FormatError(ALERT_ERROR);
        if ($tid <= 0) return Data::FormatError('请选择分类');
        if (Util::isBlank($title)) return Data::FormatError('请输入标题');
        if (!Util::IsMaxLen($title, 200)) return Data::FormatError('标题过长');
        if (!$available) return Data::FormatError('请输入发表内容');

        if (($id == 1 || $id == 2) && $tid != 1) return Data::FormatError('当前文章不允许改变分类');
        elseif (($id != 1 && $id != 2) && $tid == 1) return Data::FormatError('当前分类不允许发表文章');

        $last = Date::Datetime();

        try {
            DB::db()->BeginTransaction();

            $id = ArticleCls::Edit($id, $tid, $uid, $pic, $title, $content, $html, $roll, $act, $last, $time);

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
        if ($id == 1) return Data::FormatError("当前文章不支持删除操作");

        try {
            DB::db()->BeginTransaction();

            $id = ArticleCls::Delete($id);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Tree($org = true, $user = true, $act = null)
    {
        return Data::FormatSuccess('', ArticleCls::Tree($org, $user, $act));
    }

    //append
    public static function GetExtItems($tid, $act, $where, $order, $page, $size)
    {
        if ($tid != -1) $where .= ' AND tid=' . $tid;
        if (!is_null($act)) $where .= ' AND act=' . Util::Bool2String($act);
        return self::Items($where, $order, $page, $size);
    }

    public static function IncViews($id)
    {
        ArticleCls::IncViews($id);
    }
}

?>