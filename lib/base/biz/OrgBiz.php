<?php

class OrgBiz
{
    public static function Item($id)
    {
        $rs = OrgCls::Instance()->Item($id);
        return Data::FormatSuccess(count($rs), $rs);
    }

    public static function Name($id)
    {
        return Data::FormatSuccess(OrgCls::Instance()->Name($id));
    }

    public static function Gid($id)
    {
        return Data::FormatSuccess(OrgCls::Instance()->Gid($id));
    }

    public static function Fid($id)
    {
        return Data::FormatSuccess(OrgCls::Instance()->Fid($id));
    }

    public static function Permission($id)
    {
        return Data::FormatSuccess(OrgCls::Instance()->Permission($id));
    }

    public static function GetParentList($id, $self = true, $root = false)
    {
        $ret = array();
        $rs = OrgCls::Instance()->GetParents($id, $self, $root);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $ret[$v] = OrgCls::Instance()->Name($v);
            }
        }
        return Data::FormatSuccess(count($ret), $ret);
    }

    public static function GetNextList($id, $self = false)
    {
        $ret = array();
        $rs = OrgCls::Instance()->GetNextIds($id, $self);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $ret[$v] = OrgCls::Instance()->Name($v);
            }
        }
        return Data::FormatSuccess(count($ret), $ret);
    }

    public static function GetListByGid($gid)
    {
        $ret = array();
        $rs = OrgCls::Instance()->GetIdsByGid($gid);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $ret[$v] = OrgCls::Instance()->Name($v);
            }
        }
        return Data::FormatSuccess(count($ret), $ret);
    }

    public static function Add($fid, $oid, $name, $act)
    {
        if ($fid <= 0)
            return Data::FormatError('请选择正确的上级部门');
        if (Util::isBlank($name))
            return Data::FormatError('请输入部门名称');

        $act = Util::Bool($act);

        try {
            DB::db()->BeginTransaction();

            $id = OrgCls::Add($fid, $oid, $name, $act);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }


    public static function Edit($id, $gid, $fid, $name, $act)
    {
        if ($gid == 0)
            return Data::FormatError('请选择正确的上级部门');
        if (Util::isBlank($name))
            return Data::FormatError('请输入部门名称');

        if ($gid == -1) $gid = OrgCls::Instance()->Gid($id); //得到原组织

        $act = Util::Bool($act);

        try {
            DB::db()->BeginTransaction();

            $id = OrgCls::Eidt($id, $gid, $fid, $name, $act);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }
}

?>