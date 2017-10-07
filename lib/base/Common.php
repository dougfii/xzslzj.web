<?php

class Common
{
    //数值型团队IDS sql like 搜索
    public static function TeamLikeId($id)
    {
        return ',' . $id . ',';
    }

    public static function GetWhereInIdSql($ids)
    {
        return Util::Impode($ids);
    }

    //得到用户查询表时有何能力，默认有组织结构上下级权限和团队权限
    public static function GetTablePowerSql($uid, $res, $onlySelf = false, $hasTeam = true)
    {
        $admin = UserCls::Instance()->Admin($uid);
        $lead = UserCls::Instance()->Lead($uid);
        $oid = UserCls::Instance()->Oid($uid);

        $uids = self::GetRolePower($uid, $res);
        $uids[] = $uid;

        $sql = '';
        $team = $hasTeam ? ' OR team LIKE \'%' . Common::TeamLikeId($uid) . '%\' ' : '';

        if ($onlySelf || !($admin || $lead)) {
            $sql .= ' AND (uid IN (' . Util::Impode($uids) . ')' . $team . ')';
        } elseif ($lead && !$admin) {
            if ($oid > 0) {
                $oids = OrgCls::Instance()->GetChildrenIds($oid, true);
                if (!empty($oids)) {
                    foreach ($oids as $m => $n) {
                        $uids = array_merge($uids, UserCls::Instance()->GetIdsByOid($n));
                    }
                }
            } else {
                $uids = array_merge($uids, UserCls::Instance()->GetIdsByOid($oid));
            }


            $sql .= ' AND (uid IN (' . Util::Impode($uids) . ')' . $team . ')';
        }

        return $sql;
    }

    public static function GetRolePower($uid, $res)
    {
        $ru = array();

        $roles = RoleCls::Instance()->Items();
        foreach ($roles as $role) {
            $roleid = $role['id'];

            if (RoleCls::GetSourceApplyUser($uid, $roleid)) {
                $permissions = RoleCls::Instance()->Permissions($roleid);
                if ($res < count($permissions) && P::isView($permissions [$res])) {
                    $ru = array_merge($ru, RoleCls::Instance()->TUsers($roleid));
                }
            }
        }

        return $ru;
    }
}

?>