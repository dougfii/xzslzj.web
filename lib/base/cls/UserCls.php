<?php

//momo
class UserCls
{
    const TABLE = 't_user';
    const CACHE = 't_user';
    protected static $list = array();
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
                ORDER BY act DESC, lead DESC, id ASC
                ");
        if ($rs && count($rs) > 0) {
            foreach ($rs as $k => $v) {
                self::$list [$v ['id']] = $v;
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

    public function Gid($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['gid'];
        return -1;
    }

    public function Oid($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['oid'];
        return -1;
    }

    public function Username($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['username'];
        return '';
    }

    public function Name($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['name'];
        return '';
    }

    public function Serial($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['serial'];
        return '';
    }

    public function Sex($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['sex'];
        return true;
    }

    public function Moblie($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['moblie'];
        return '';
    }

    public function IMEI($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['imei'];
        return '';
    }

    public function Email($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['email'];
        return '';
    }

    public function Lead($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['lead'];
        return false;
    }

    public function Admin($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['admin'];
        return false;
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

    public static function Add($gid, $oid, $username, $name, $password, $serial, $sex, $mobile, $imei, $email, $lead, $admin, $act)
    {
        $rs = DB::db()->Fetch("
				INSERT INTO " . self::TABLE . "(gid, oid, username, name, password, serial, sex, mobile, imei, email, lead, admin, act)
				VALUES(?,?,?,?,md5(?),?,?,?,?,?,?,?,?)
				RETURNING id
				", array($gid, $oid, $username, $name, $password, $serial, $sex, $mobile, $imei, $email, $lead, $admin, $act));
        self::Clear();
        return $rs ['id'];
    }

    public static function Edit($id, $gid, $oid, $name, $serial, $sex, $mobile, $imei, $email, $lead, $admin, $act)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET gid=?, oid=?, name=?, serial=?, sex=?, mobile=?, imei=?, email=?, lead=?, admin=?, act=?
				WHERE id=?
				", array($gid, $oid, $name, $serial, $sex, $mobile, $imei, $email, $lead, $admin, $act, $id));
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

    public static function EditPassword($id, $password)
    {
        DB::db()->Query("
				 UPDATE " . self::TABLE . "
				 SET password=md5(?)
				 WHERE id=?
				 ", array($password, $id));
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

    public function ExistName($name, $selfid = null)
    {
        foreach (self::$list as $k => $v) {
            if (empty ($selfid)) {
                if (strtolower($v ['name']) == strtolower($name))
                    return true;
            } else {
                if (strtolower($v ['name']) == strtolower($name) && $v ['id'] != $selfid)
                    return true;
            }
        }

        return false;
    }

    public function ExistSerail($name, $selfid = null)
    {
        foreach (self::$list as $k => $v) {
            if (empty ($selfid)) {
                if (strtolower($v ['serial']) == strtolower($name))
                    return true;
            } else {
                if (strtolower($v ['serial']) == strtolower($name) && $v ['id'] != $selfid)
                    return true;
            }
        }

        return false;
    }

    public function LikeIds($key, $field)
    {
        $rs = array();
        foreach (self::$list as $k => $v) {
            if (strtolower($v [$field]) == strtolower($key))
                $rs[] = $k;
        }
        return $rs;
    }

    public function Login($username, $password)
    {
        foreach (self::$list as $k => $v) {
            if (strtolower($v ['username']) == strtolower($username) && $v ['password'] == md5($password)) {
                if ($v ['act'])
                    return $v;
                return false;
            }
        }

        return null;
    }

    public function GetIdsByGid($gid)
    {
        $rs = array();
        foreach (self::$list as $k => $v) {
            if ($v ['gid'] == $gid)
                $rs [] = $k;
        }
        return $rs;
    }

    public function GetIdsByOid($oid)
    {
        $rs = array();
        foreach (self::$list as $k => $v) {
            if ($v ['oid'] == $oid)
                $rs [] = $k;
        }
        return $rs;
    }

    public function GetIdsByGidAndOid($gid, $oid)
    {
        $rs = array();
        foreach (self::$list as $k => $v) {
            if ($v ['gid'] == $gid && $v ['oid'] == $oid)
                $rs [] = $k;
        }
        return $rs;
    }

    public function GetGroupParents($id)
    {
        $gid = $this->Gid($id);
        return GroupCls::Instance()->GetParents($gid);
    }

    public function GetOrgParents($id)
    {
        $oid = $this->Oid($id);
        return OrgCls::Instance()->GetParents($oid);
    }

    public function GetIdByUsername($username)
    {
        foreach (self::$list as $k => $v) {
            if (strtolower($v ['username']) == strtolower($username))
                return $k;
        }

        return 0;
    }
}

?>