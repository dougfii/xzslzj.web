<?php

//momo
class GroupCls
{
    const TABLE = 't_group';
    const CACHE = 't_group';
    private static $list = array();
    private static $instance = null;

    public static function Instance()
    {
        if (self::$instance == null) {
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

        if ($list !== false && is_array($list)) {
            self::$list = $list;
            return;
        }

        $rs = DB::db()->FetchAll("
                SELECT * FROM " . self::TABLE . "
                WHERE del=false
                ORDER BY id ASC
                ");
        if ($rs && count($rs) > 0) {
            foreach ($rs as $k => $v) {
                self::$list [$v ['id']] = $v;
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

    public function Fid($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['fid'];
        return -1;
    }

    public function Name($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['name'];
        return '';
    }

    public function Company($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['company'];
        return '';
    }

    public function Permission($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['permission'];
        return '';
    }

    public function Act($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['act'];
        return false;
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

    public static function Add($fid, $name, $company, $act)
    {
        $rs = DB::db()->Fetch("
				INSERT INTO " . self::TABLE . "(fid, name, company, act)
				VALUES(?,?,?,?)
				RETURNING id
				", array($fid, $name, $company, $act));
        self::Clear();
        return $rs ['id'];
    }

    public static function Edit($id, $fid, $name, $company, $act)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET fid=?, name=?, company=?, act=?
				WHERE id=?
				", array($fid, $name, $company, $act, $id));
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

    public static function EditPermission($id, $permission)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET permission=?
				WHERE id=?
				", array($permission, $id));
        self::Clear();
    }

    public static function EditPermissions($ids, $permission)
    {
        $ids = Util::Impode($ids);
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET permission=?
				WHERE id IN (" . $ids . ")
				", array($permission));
        self::Clear();
    }

    public static function InitPermission($permission)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET permission=?
				", array($permission));
        self::Clear();
    }

    public function ExistName($fid, $name, $selfid = null)
    {
        foreach (self::$list as $k => $v) {
            if (empty ($selfid)) {
                if ($v ['fid'] == $fid && strtolower($v ['name']) == strtolower($name))
                    return true;
            } else {
                if ($v ['fid'] == $fid && strtolower($v ['name']) == strtolower($name) && $v ['id'] != $selfid)
                    return true;
            }
        }

        return false;
    }

    //得到下一级ID列表
    public function GetNextIds($id, $self = false)
    {
        $rs = array();
        if ($self)
            $rs [] = $id;
        foreach (self::$list as $k => $v) {
            if ($v ['fid'] == $id)
                $rs [] = $k;
        }
        return $rs;
    }

    //得到子级(递归)ID列表
    public function GetChildrenIds($id, $self = false)
    {
        $rs = array();
        if ($self)
            $rs [] = $id;

        $next = self::GetNextIds($id);
        if (!empty($next)) {
            foreach ($next as $k => $v) {
                $rs = array_merge($rs, self::GetChildrenIds($v, true));
            }
        }

        return $rs;
    }

    //按从上至下的顺序得到父级ID列表
    public function GetParents($id, $self = true, $root = false)
    {
        $rs = array();
        if ($self)
            $rs[] = $id;
        if ($id > 0) {
            do {
                $fid = $id;
                $id = $this->FID($id);
                if ($id > 0) {
                    $rs [] = $id;
                } else {
                    if ($root)
                        $rs [] = $fid;
                }
            } while ($id > 0);
        }
        return array_reverse($rs);
    }

    public function Tree($gid = true, $org = true, $user = true, $act = null)
    {
        $rs = array();

        //根
        $rs = array(array('id' => 'G0', 'fid' => 'G0', 'gid' => '', 'oid' => '', 'uid' => '', 'name' => '组织机构', 'open' => true, 'icon' => '/img/ztree_group0.gif', 'type' => 1));

        foreach (self::$list as $k => $v) {
            if ($gid === true || $gid == $v['id'])
                $rs [] = array('id' => 'G' . $v ['id'], 'fid' => 'G' . $v ['fid'], 'gid' => $v ['id'], 'oid' => '', 'uid' => '', 'name' => $v ['name'], 'open' => true, 'icon' => '/img/ztree_group1.gif', 'type' => 1);
        }

        //部门
        if ($org) {
            $ro = OrgCls::Instance()->Items();
            foreach ($ro as $k => $v) {
                $fid = $v ['fid'] == 0 ? 'G' . $v ['gid'] : 'O' . $v ['fid'];
                $rs [] = array('id' => 'O' . $v ['id'], 'fid' => $fid, 'gid' => $v ['gid'], 'oid' => $v ['id'], 'uid' => '', 'name' => $v ['name'], 'open' => true, 'icon' => '/img/ztree_org.gif', 'type' => 2);
            }
        }

        //用户
        if ($org && $user) {
            $ru = UserCls::Instance()->Items();
            foreach ($ru as $k => $v) {
                $fid = ($v['oid'] == 0) ? 'G' . $v ['gid'] : 'O' . $v ['oid'];

                $icon = $v ['act'] ? ($v ['lead'] ? '/img/ztree_ulead.gif' : ($v ['sex'] ? '/img/ztree_uwoman.gif' : '/img/ztree_uman.gif')) : '/img/ztree_uact.gif';
                $rs [] = array('id' => 'U' . $v ['id'], 'fid' => $fid, 'gid' => $v ['gid'], 'oid' => $v ['oid'], 'uid' => $v ['id'], 'name' => $v ['name'], 'open' => false, 'icon' => $icon, 'type' => 3);

            }
        }

        return $rs;
    }
}

?>