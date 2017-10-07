<?php

//momo
class UserBiz
{
    public static function Item($id)
    {
        $rs = UserCls::Instance()->Item($id);
        return Data::FormatSuccess(count($rs), $rs);
    }

    public static function Gid($id)
    {
        return Data::FormatSuccess(UserCls::Instance()->Gid($id));
    }

    public static function Name($id)
    {
        return Data::FormatSuccess(UserCls::Instance()->Name($id));
    }

    public static function Login($username, $password)
    {
        if (!Util::IsUsername($username) || !Util::IsPassword($password))
            Data::FormatError('用户或密码错误');

        $rs = UserCls::Instance()->Login($username, $password);
        if (is_null($rs))
            return Data::FormatError('用户或密码错误');
        if (!$rs)
            return Data::FormatError('账号被禁止登录,请与管理员联系');

        LogLoginCls::Add(2, $rs['id'], Inet::GetIP());

        return Data::FormatSuccess('', $rs);
    }

    public static function GetListByGidAndOid($gid, $oid)
    {
        $ret = array();
        $rs = UserCls::Instance()->GetIdsByGidAndOid($gid, $oid);
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $ret[$v] = UserCls::Instance()->Item($v);
            }
        }
        return Data::FormatSuccess(count($ret), $ret);
    }

    public static function InitAdminPermission()
    {
        $p = P::Init(true);
        UserCls::EditPermission(1, $p);
    }

    public static function Add($gid, $oid, $username, $name, $password, $serial, $sex, $mobile, $imei, $email, $lead, $admin, $act)
    {
        if ($oid <= 0) return Data::FormatError('请选择一个部门');
        if (Util::isBlank($username)) return Data::FormatError('请输入登录名称');
        if (!Util::IsMaxLen($username, 20)) return Data::FormatError('登录名称过长');
        if (!Util::IsPassword($password)) return Data::FormatError('请输入正确的初始密码');
        if (!Util::IsMaxLen($serial, 20)) return Data::FormatError('员工编号过长');
        if (Util::IsBlank($name)) return Data::FormatError('请输入姓名');
        if (!Util::IsMaxLen($name, 20)) return Data::FormatError('姓名过长');

        $lead = Util::Bool2Int($lead);
        $sex = Util::Bool2Int($sex);
        $act = Util::Bool2Int($act);
        $admin = Util::Bool2Int($admin);

        try {
            DB::db()->BeginTransaction();

            $id = UserCls::Add($gid, $oid, $username, $name, $password, $serial, $sex, $mobile, $imei, $email, $lead, $admin, $act);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function GetIdByUsername($username)
    {
        return Data::FormatSuccess(UserCls::Instance()->GetIdByUsername($username));
    }


}

?>