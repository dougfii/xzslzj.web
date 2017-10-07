<?php

class ArticleTypeCls
{
    const TABLE = 't_article_type';
    const CACHE = 't_article_type';
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

    public function Pid($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['pid'];
        return -1;
    }

    public function Name($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['name'];
        return '';
    }

    public function Act($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['act'];
        return false;
    }

    public function Count($act = true)
    {
        return count($this->Items($act));
    }

    public function Items($act = true)
    {
        if (is_null($act)) return self::$list;

        $rs = array();
        foreach (self::$list as $k => $v) {
            if ($v['act'] == $act) $rs[$k] = $v;
        }
        return $rs;
    }

    public static function Clear()
    {
        $cache = new Cache ();
        $cache->Delete('system', self::CACHE);
    }

    public static function Add($pid, $name, $dir, $act)
    {
        $rs = DB::db()->Fetch("
				INSERT INTO " . self::TABLE . "(pid, name, dir, act)
				VALUES(?,?,?,?)
				RETURNING id
				", array($pid, $name, $dir, $act));
        self::Clear();
        return $rs ['id'];
    }

    public static function Edit($id, $pid, $name, $dir, $act)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET pid=?, name=?, dir=?, act=?
				WHERE id=?
				", array($pid, $name, $dir, $act, $id));
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

    public static function ExistName($pid, $name, $selfid = null)
    {
        foreach (self::Instance()->Items(null) as $k => $v) {
            if (empty ($selfid)) {
                if ($v ['pid'] == $pid && strtolower($v ['name']) == strtolower($name))
                    return true;
            } else {
                if ($v ['pid'] == $pid && strtolower($v ['name']) == strtolower($name) && $v ['id'] != $selfid)
                    return true;
            }
        }

        return false;
    }

    //得到下一级ID列表
    public static function GetNextIds($id, $self = false, $dir = null, $act = true)
    {
        $rs = array();
        if ($self)
            $rs [] = $id;
        foreach (self::Instance()->Items($act) as $k => $v) {
            if ($v ['pid'] == $id) {
                if (is_null($dir)) $rs[] = $k;
                else {
                    if ($v['dir'] == $dir)
                        $rs [] = $k;
                }
            }
        }
        return $rs;
    }

    //得到子级(递归)ID列表
    public static function GetChildrenIds($id, $self = false, $dir = null, $act = true)
    {
        $rs = array();
        if ($self)
            $rs [$id] = $id;

        $next = self::GetNextIds($id, $self, $dir, $act);
        if (!empty($next)) {
            foreach ($next as $k => $v) {
                $rs[$v] = $v;
                $rs = array_merge($rs, self::GetChildrenIds($v, false, $dir, $act));
            }
        }

        return $rs;
    }

    //按从上至下的顺序得到父级ID列表
    public static function GetParents($id, $self = true, $root = false)
    {
        $rs = array();
        if ($self)
            $rs[] = $id;
        if ($id > 0) {
            do {
                $pid = $id;
                $id = self::Instance()->Pid($id);
                if ($id > 0) {
                    $rs [] = $id;
                } else {
                    if ($root)
                        $rs [] = $pid;
                }
            } while ($id > 0);
        }
        return array_reverse($rs);
    }

    public static function Tree($act = true)
    {
        $rs = array();

        //根
        $rs = array(array('id' => '0', 'pid' => '0', 'name' => '文章分类', 'open' => true, 'icon' => '/img/ztree_croot.gif', 'type' => 1));

        $ru = self::Instance()->Items($act);
        foreach ($ru as $k => $v) {
            $icon = $v ['act'] ? '/img/ztree_con.gif' : '/img/ztree_coff.gif';
            $rs [] = array('id' => $v ['id'], 'pid' => $v['pid'], 'name' => $v ['name'], 'open' => true, 'icon' => $icon, 'type' => 2);
        }

        return $rs;
    }
}