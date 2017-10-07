<?php

class MemberBiz
{
    public static function Item($id)
    {
        return MemberCls::Instance()->Item($id);
    }

    public static function Gid($id)
    {
        return MemberCls::Instance()->gid($id);
    }

    public static function Name($id)
    {
        return MemberCls::Instance()->Name($id);
    }

    public static function Company($id)
    {
        return MemberCls::Instance()->Company($id);
    }

    public static function Act($id)
    {
        return MemberCls::Instance()->Act($id);
    }

    public static function Items($where, $order, $page, $size)
    {
        $where = 'WHERE del=false ' . $where;
        $order = empty($order) ? 'ORDER BY id DESC' : $order;

        $count = MemberCls::Count($where);
        $page = HTML::PageNum($count, $page, $size);
        $start = HTML::PagePos($count, $page, $size);
        $rs = MemberCls::Items($where, $order, $start, $size);

        return Data::FormatSuccess($count, $rs);
    }

    public static function Add($gid, $name, $company, $pass, $repass, $contacts, $mobile, $email)
    {
        if ($gid <= 0) return Data::FormatError('请选择所属区域');
        if (Util::isBlank($name)) return Data::FormatError('请输入工程名称');
        if (!Util::IsMaxLen($name, 200)) return Data::FormatError('工程名称过长');
        if (Util::isBlank($company)) return Data::FormatError('请输入申请单位');
        if (!Util::IsMaxLen($company, 200)) return Data::FormatError('申请单位过长');
        if (!Util::IsPassword($pass)) return Data::FormatError('请设置有效的登录密码');
        if ($pass != $repass) return Data::FormatError('登录密码与重复密码不一致');
        if (Util::isBlank($contacts)) return Data::FormatError('请输入联系人');
        if (!Util::IsMaxLen($contacts, 50)) return Data::FormatError('联系人过长');
        if (!Util::IsMobile($mobile)) return Data::FormatError('请输入正确的联系人手机号码');
        if (!Util::isBlank($email) && !Util::IsEmail($email)) return Data::FormatError('请输入正确的联系人电子邮箱');

        if (MemberCls::ExistName($name)) return Data::FormatError('工程名称已经存在');

        try {
            DB::db()->BeginTransaction();

            $id = MemberCls::Add($gid, $name, $company, $pass, $contacts, $mobile, $email);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }

    public static function Edit($id, $tid, $uid, $pic, $title, $content, $html, $available, $roll, $act)
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

            $id = MemberCls::Edit($id, $tid, $uid, $pic, $title, $content, $html, $roll, $act, $last);

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

            $id = MemberCls::Delete($id);

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

            $id = MemberCls::SetAct($id);

            DB::db()->Commit();

            return Data::FormatSuccess($id);
        } catch (Exception $e) {
            DB::db()->RollBack();
        }

        return Data::FormatError(ALERT_ERROR);
    }


    public static function Login($name, $pass)
    {
        if (Util::IsBlank($name) || !Util::IsPassword($pass)) Data::FormatError('工程名称或登录密码错误');

        $rs = MemberCls::Login($name, $pass);
        if (is_null($rs)) return Data::FormatError('工程名称或登录密码错误');
        // if (!$rs) return Data::FormatError('账号被禁止登录,请与管理员联系');

        LogLoginCls::Add(1, $rs['id'], Inet::GetIP());

        return Data::FormatSuccess('', $rs);
    }

    //主管单位名称
    public static function GetGroupCompany($mid)
    {
        return GroupBiz::Company(self::Gid($mid));
    }
}

?>