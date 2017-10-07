<?php

//laughing
class RoleBiz
{
    public static function Item($id)
    {
        $rs = RoleCls::Instance()->Item($id);
        return Data::FormatSuccess(count($rs), $rs);
    }

    public static function Items()
    {
        $count = RoleCls::Instance()->Count();
        $rs = RoleCls::Instance()->Items();

        return Data::FormatSuccess($count, $rs);
    }

    public static function Add($name, $sgroupi, $sorgi, $sgroup, $sorg, $suser, $tgroupi, $torgi, $tgroup, $torg, $tuser, $s, $act)
    {
        if (Util::IsBlank($name))
            return Data::FormatError('请输入角色名称');
        if (!Util::IsMaxLen($name, 200))
            return Data::FormatError('角色名称过长');

        $sgroupi = Util::Bool2Int($sgroupi);
        $sorgi = Util::Bool2Int($sorgi);
        $tgroupi = Util::Bool2Int($tgroupi);
        $torgi = Util::Bool2Int($torgi);

        $j = P::Clean($s);
        try
        {
            DB::db()->BeginTransaction();

            $id = RoleCls::Add($name, $sgroupi, $sorgi, $sgroup, $sorg, $suser, $tgroupi, $torgi, $tgroup, $torg, $tuser, $j, $act);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        }
        catch (Exception $e)
        {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Edit($id, $name, $sgroupi, $sorgi, $sgroup, $sorg, $suser, $tgroupi, $torgi, $tgroup, $torg, $tuser, $s, $act)
    {
        if (Util::IsBlank($name))
            return Data::FormatError('请输入角色名称');
        if (!Util::IsMaxLen($name, 200))
            return Data::FormatError('角色名称过长');

        $sgroupi = Util::Bool2Int($sgroupi);
        $sorgi = Util::Bool2Int($sorgi);
        $tgroupi = Util::Bool2Int($tgroupi);
        $torgi = Util::Bool2Int($torgi);

        $j = P::Clean($s);
        try
        {
            DB::db()->BeginTransaction();

            RoleCls::Edit($id, $name, $sgroupi, $sorgi, $sgroup, $sorg, $suser, $tgroupi, $torgi, $tgroup, $torg, $tuser, $j, $act);

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

            RoleCls::Delete($id);

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