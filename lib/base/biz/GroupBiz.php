<?php

class GroupBiz
{
    public static function Item($id)
    {
        $rs = GroupCls::Instance()->Item($id);
        return Data::FormatSuccess(count($rs), $rs);
    }

    public static function Name($id)
    {
        return Data::FormatSuccess(GroupCls::Instance()->Name($id));
    }

    public static function Company($id)
    {
        return GroupCls::Instance()->Company($id);
    }

    public static function Fid($id)
    {
        return Data::FormatSuccess(GroupCls::Instance()->Fid($id));
    }

    public static function Permission($id)
    {
        return Data::FormatSuccess(GroupCls::Instance()->Permission($id));
    }

    public static function Items()
    {
        return Data::FormatSuccess(GroupCls::Instance()->Count(), GroupCls::Instance()->Items());
    }

    public static function GetParentList($id, $self = true, $root = false)
    {
        $ret = array();
        $rs = GroupCls::Instance()->GetParents($id, $self, $root);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $ret[$v] = GroupCls::Instance()->Name($v);
            }
        }
        return Data::FormatSuccess(count($ret), $ret);
    }

    public static function GetNextList($id, $self = false)
    {
        $ret = array();
        $rs = GroupCls::Instance()->GetNextIds($id, $self);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $ret[$v] = GroupCls::Instance()->Name($v);
            }
        }
        return Data::FormatSuccess(count($ret), $ret);
    }

    public static function Tree($gid = true, $org = true, $user = true, $act = null)
    {
        return Data::FormatSuccess('', GroupCls::Instance()->Tree($gid, $org, $user, $act));
    }

    public static function Add($fid, $name, $company, $act)
    {
        if (Util::isBlank($name)) return Data::FormatError('请输入组织名称');
        if (Util::isBlank($company)) return Data::FormatError('请输入主管单位名称');

        $act = Util::Bool($act);

        try {
            DB::db()->BeginTransaction();

            $id = GroupCls::Add($fid, $name, $company, $act);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Edit($id, $fid, $name, $company, $act)
    {
        if ($id <= 0) return Data::FormatError(ALERT_ERROR);
        if (Util::isBlank($name)) return Data::FormatError('请输入组织名称');
        if (Util::isBlank($company)) return Data::FormatError('请输入主管单位名称');

        $act = Util::Bool($act);

        try {
            DB::db()->BeginTransaction();

            GroupCls::Edit($id, $fid, $name, $company, $act);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }
}

?>