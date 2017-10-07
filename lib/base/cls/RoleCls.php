<?php

class RoleCls
{
    const TABLE = 't_role';
    const CACHE = 't_role';
    protected static $list = array();
    private static $instance = null;

    public static function Instance()
    {
        if (self::$instance == null)
        {
            self::$instance = new self ();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->Load();
    }

    private function Load()
    {
        $cache = new Cache ();
        $list = $cache->get('system', self::CACHE);

        if ($list !== false && is_array($list))
        {
            self::$list = $list;
            return;
        }

        $rs = DB::db()->FetchAll("
                SELECT * FROM " . self::TABLE . "
                WHERE del=false
                ORDER BY id ASC
                ");
        if ($rs && count($rs) > 0)
        {
            foreach ($rs as $k => $v)
            {
                self::$list [$v ['id']] = $v;
                self::$list [$v ['id']] ['sgroups'] = Util::Explode(self::$list [$v ['id']] ['sgroup']);
                self::$list [$v ['id']] ['sorgs'] = Util::Explode(self::$list [$v ['id']] ['sorg']);
                self::$list [$v ['id']] ['susers'] = Util::Explode(self::$list [$v ['id']] ['suser']);
                self::$list [$v ['id']] ['tgroups'] = Util::Explode(self::$list [$v ['id']] ['tgroup']);
                self::$list [$v ['id']] ['torgs'] = Util::Explode(self::$list [$v ['id']] ['torg']);
                self::$list [$v ['id']] ['tusers'] = Util::Explode(self::$list [$v ['id']] ['tuser']);
                self::$list [$v ['id']] ['permissions'] = Util::Explode(self::$list [$v ['id']] ['permission']);
            }

            $cache = new Cache ();
            $cache->Set(self::$list, 'system', self::CACHE);
        }
    }

    public function Item($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id];
        return array();
    }

    public function Name($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['name'];
        return '';
    }

    public function SGroupI($id)
    {
        if (array_key_exists($id, self::$list))
        {
            return self::$list [$id] ['sgroupi'];
        }
        return false;
    }

    public function SOrgI($id)
    {
        if (array_key_exists($id, self::$list))
        {
            return self::$list [$id] ['sorgi'];
        }
        return false;
    }

    public function SGroup($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['sgroup'];
        return '';
    }

    public function SOrg($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['sorg'];
        return '';
    }

    public function SUser($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['suser'];
        return '';
    }

    public function SGroups($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['sgroups'];
        return array();
    }

    public function SOrgs($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['sorgs'];
        return array();
    }

    public function SUsers($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['susers'];
        return array();
    }

    public function TGroupI($id)
    {
        if (array_key_exists($id, self::$list))
        {
            return self::$list [$id] ['tgroupi'];
        }
        return false;
    }

    public function TOrgI($id)
    {
        if (array_key_exists($id, self::$list))
        {
            return self::$list [$id] ['torgi'];
        }
        return false;
    }

    public function TGroup($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['tgroup'];
        return '';
    }

    public function TOrg($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['torg'];
        return '';
    }

    public function TUser($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['tuser'];
        return '';
    }

    public function TGroups($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['tgroups'];
        return array();
    }

    public function TOrgs($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['torgs'];
        return array();
    }

    public function TUsers($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['tusers'];
        return array();
    }

    public function Permission($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['permission'];
        return '';
    }

    public function Permissions($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['permissions'];
        return array();
    }

    public function Count()
    {
        return count(self::$list);
    }

    public function Items()
    {
        return self::$list;
    }

    public static function Clear()
    {
        $cache = new Cache ();
        $cache->Delete('system', self::CACHE);
    }

    public static function Add($name, $sgroupi, $sorgi, $sgroup, $sorg, $suser, $tgroupi, $torgi, $tgroup, $torg, $tuser, $j, $act)
    {
        $rs = DB::db()->Fetch("
                INSERT INTO " . self::TABLE . "(name, sgroupi, sorgi, sgroup, sorg, suser, tgroupi, torgi, tgroup, torg, tuser, permission, act)
				VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)
				RETURNING id
				", array($name, $sgroupi, $sorgi, $sgroup, $sorg, $suser, $tgroupi, $torgi, $tgroup, $torg, $tuser, $j, $act));
        self::Clear();
        return $rs ['id'];
    }

    public static function Edit($id, $name, $sgroupi, $sorgi, $sgroup, $sorg, $suser, $tgroupi, $torgi, $tgroup, $torg, $tuser, $j, $act)
    {
        DB::db()->Query("
                UPDATE " . self::TABLE . "
                SET name=?, sgroupi=?, sorgi=?, sgroup=?, sorg=?, suser=?, tgroupi=?, torgi=?, tgroup=?, torg=?, tuser=?, permission=?, act=?
                WHERE id=?
                ", array($name, $sgroupi, $sorgi, $sgroup, $sorg, $suser, $tgroupi, $torgi, $tgroup, $torg, $tuser, $j, $act, $id));
        self::Clear();
    }

    public static function Delete($id)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET del=true
				WHERE id=?
				", array($id));
        self::Clear();
    }

    public static function GetSGroupApplyUser($uid, $roleid)
    {
        $sgroups = self::Instance()->SGroups($roleid);
        $sgroupi = self::Instance()->SGroupI($roleid);

        foreach ($sgroups as $sgroup)
        {
            if ($sgroupi)
            {
                $gids = GroupCls::Instance()->GetChildrenIds($sgroup, true);
                foreach ($gids as $gid)
                {
                    $oids = OrgCls::Instance()->GetIdsByGid($gid);
                    foreach ($oids as $oid)
                    {
                        $suboids = OrgCls::Instance()->GetChildrenIds($oid, true);
                        $uids = UserCls::Instance()->GetIdsByOid($suboids);
                        foreach ($uids as $id)
                        {
                            if ($uid == $id)
                                return true;
                        }
                    }
                }
            }
            else
            {
                $uids = UserCls::Instance()->GetIdsByGid($sgroup);
                foreach ($uids as $id)
                {
                    if ($uid == $id)
                        return true;
                }
            }
        }

        return false;
    }

    public static function GetSOrgApplyUser($uid, $roleid)
    {
        $sorgs = self::Instance()->SOrgs($roleid);
        $sorgi = self::Instance()->SOrgI($roleid);
        foreach ($sorgs as $sorg)
        {
            if ($sorgi)
            {
                $suboids = OrgCls::Instance()->GetChildrenIds($sorg, true);
                $uids = UserCls::Instance()->GetIdsByOid($suboids);
                foreach ($uids as $id)
                {
                    if ($uid == $id)
                        return true;
                }

            }
            else
            {
                $uids = UserCls::Instance()->GetIdsByOid($sorg);
                foreach ($uids as $id)
                {
                    if ($uid == $id)
                        return true;
                }
            }
        }

        return false;
    }

    public static function GetSUserApplyUser($uid, $roleid)
    {
        $susers = self::Instance()->SUsers($roleid);
        foreach ($susers as $suser)
        {
            if ($uid == $suser)
            {
                return true;
            }
        }
        return false;
    }

    public static function GetSourceApplyUser($uid, $roleid)
    {
        return self::GetSGroupApplyUser($uid, $roleid) || self::GetSOrgApplyUser($uid, $roleid) || self::GetSUserApplyUser($uid, $roleid);
    }
}

?>