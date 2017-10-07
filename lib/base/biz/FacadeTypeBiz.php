<?php

class FacadeTypeBiz
{
    public static function Item($id)
    {
        return FacadeTypeCls::Instance()->Item($id);
    }

    public static function Name($id)
    {
        return FacadeTypeCls::Instance()->Name($id);
    }

    public static function Pid($id)
    {
        return FacadeTypeCls::Instance()->Pid($id);
    }

    public static function Score($id)
    {
        return FacadeTypeCls::Instance()->Score($id);
    }

    public static function GetNextList($id, $self = false)
    {
        $ret = array();
        $rs = FacadeTypeCls::GetNextIds($id, $self);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $ret[$v] = FacadeTypeCls::Instance()->Item($v);
            }
        }
        return array(count($ret), $ret);
    }

    public static function GetChildrenIds($id, $self = false)
    {
        $rs = FacadeTypeCls::GetChildrenIds($id, $self);
        return array(count($rs), $rs);
    }

    public static function GetParentList($id, $self = true, $root = false)
    {
        $ret = array();
        $rs = FacadeTypeCls::GetParents($id, $self, $root);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $ret[$v] = FacadeTypeCls::Instance()->Name($v);
            }
        }
        return array(count($ret), $ret);
    }

    public static function Add($pid, $name, $score)
    {
        if (Util::isBlank($name)) return Data::FormatError('请输入项目名称');
        if (!Util::IsMaxLen($name, 50)) return Data::FormatError('项目名称过长');
        if (FacadeTypeCls::ExistName($pid, $name)) return Data::FormatError('项目名称已经存在');


        try {
            DB::db()->BeginTransaction();

            $id = FacadeTypeCls::Add($pid, $name, $score);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Edit($id, $pid, $name, $score)
    {
        if ($id <= 0) return Data::FormatError(ALERT_ERROR);
        if (Util::isBlank($name)) return Data::FormatError('请输入项目名称');
        if (!Util::IsMaxLen($name, 50)) return Data::FormatError('项目名称过长');
        if (FacadeTypeCls::ExistName($pid, $name, $id)) return Data::FormatError('项目名称已经存在');

        try {
            DB::db()->BeginTransaction();

            $id = FacadeTypeCls::Edit($id, $pid, $name, $score);

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
        $orgs = FacadeTypeCls::GetNextIds($id);
        if (!empty($orgs)) return Data::FormatError("当前项目存在下级项目，不能删除");

        try {
            DB::db()->BeginTransaction();

            $id = FacadeTypeCls::Delete($id);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Tree()
    {
        return FacadeTypeCls::Tree();
    }
}

?>