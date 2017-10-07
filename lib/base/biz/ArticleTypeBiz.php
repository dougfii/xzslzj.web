<?php

class ArticleTypeBiz
{
    public static function Item($id)
    {
        return Data::FormatSuccess(1, ArticleTypeCls::Instance()->Item($id));
    }

    public static function Name($id)
    {
        return Data::FormatSuccess(ArticleTypeCls::Instance()->Name($id));
    }

    public static function Pid($id)
    {
        return Data::FormatSuccess(ArticleTypeCls::Instance()->Pid($id));
    }

    public static function Permission($id)
    {
        return Data::FormatSuccess(ArticleTypeCls::Instance()->Permission($id));
    }

    public static function GetNextList($id, $self = false, $dir = null, $act = true)
    {
        $ret = array();
        $rs = ArticleTypeCls::GetNextIds($id, $self, $dir, $act);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $ret[$v] = ArticleTypeCls::Instance()->Item($v);
            }
        }
        return Data::FormatSuccess(count($ret), $ret);
    }

    public static function GetChildrenIds($id, $self = false, $dir = null, $act = true)
    {
        $rs = ArticleTypeCls::GetChildrenIds($id, $self, $dir, $act);
        return Data::FormatSuccess(count($rs), $rs);
    }

    public static function GetParentList($id, $self = true, $root = false)
    {
        $ret = array();
        $rs = ArticleTypeCls::GetParents($id, $self, $root);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $ret[$v] = ArticleTypeCls::Instance()->Name($v);
            }
        }
        return Data::FormatSuccess(count($ret), $ret);
    }

    public static function Add($pid, $name, $dir, $act)
    {
        if (Util::isBlank($name)) return Data::FormatError('请输入分类名称');
        if (!Util::IsMaxLen($name, 50)) return Data::FormatError('分类名称过长');
        if (ArticleTypeCls::ExistName($pid, $name)) return Data::FormatError('分类名称已经存在');

        $act = Util::Bool2Int($act);

        try {
            DB::db()->BeginTransaction();

            $id = ArticleTypeCls::Add($pid, $name, $dir, $act);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Edit($id, $pid, $name, $dir, $act)
    {
        if ($id <= 0) return Data::FormatError(ALERT_ERROR);
        if (Util::isBlank($name)) return Data::FormatError('请输入分类名称');
        if (!Util::IsMaxLen($name, 50)) return Data::FormatError('分类名称过长');
        if (ArticleTypeCls::ExistName($pid, $name, $id)) return Data::FormatError('分类名称已经存在');

        try {
            DB::db()->BeginTransaction();

            $id = ArticleTypeCls::Edit($id, $pid, $name, $dir, $act);

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
        if ($id == 1) return Data::FormatError('不允许删除第一个分类');
        $orgs = ArticleTypeCls::GetNextIds($id);
        if (!empty($orgs)) return Data::FormatError("当前分类存在下级分类，不能删除");
        //TODO: 检查内容列表
        // $users = UserCls::GetIdsByOid($id);
        // if (!empty($users)) return Data::FormatError("当前部门存在用户，不能删除");

        try {
            DB::db()->BeginTransaction();

            $id = ArticleTypeCls::Delete($id);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Tree($act = true)
    {
        return Data::FormatSuccess('', ArticleTypeCls::Tree($act));
    }
}

?>