<?php

//momo
class OrgCls
{
    const TABLE = 't_org';
    const CACHE = 't_org';
    private static $list = array();
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

    public static function Add($fid, $oid, $name, $act)
    {
        $rs = DB::db()->Fetch("
				INSERT INTO " . self::TABLE . "(gid, fid, name, act)
				VALUES(?,?,?,?)
				RETURNING id
				", array($fid, $oid, $name, $act));
        self::Clear();
        return $rs ['id'];
    }

    public static function Edit($id, $gid, $fid, $name, $act)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET gid=?, fid=?, name=?, act=?
				WHERE id=?
				", array($gid, $fid, $name, $act, $id));
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

    public static function ExistName($fid, $name, $selfid = null)
    {
        foreach (self::$list as $k => $v)
        {
            if (empty ($selfid))
            {
                if ($v ['fid'] == $fid && strtolower($v ['name']) == strtolower($name))
                    return true;
            }
            else
            {
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
        foreach (self::$list as $k => $v)
        {
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
        if (!empty($next))
        {
            foreach ($next as $k => $v)
            {
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
        if ($id > 0)
        {
            do
            {
                $fid = $id;
                $id = $this->FID($id);
                if ($id > 0)
                {
                    $rs [] = $id;
                }
                else
                {
                    if ($root)
                        $rs [] = $fid;
                }
            } while ($id > 0);
        }
        return array_reverse($rs);
    }

    //通过GID得到ID列表
    public function GetIdsByGid($gid)
    {
        $rs = array();
        foreach (self::$list as $k => $v)
        {
            if ($v ['gid'] == $gid)
                $rs [] = $k;
        }
        return $rs;
    }
}

?>