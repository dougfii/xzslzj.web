<?php

class FacadeTypeCls
{
    const TABLE = 't_facade_type';
    const CACHE = 't_facade_type';
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

    public function Score($id)
    {
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['score'];
        return 0;
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

    public static function Add($pid, $name, $score)
    {
        $rs = DB::db()->Fetch("
				INSERT INTO " . self::TABLE . "(pid, name, score)
				VALUES(?,?,?)
				RETURNING id
				", array($pid, $name, $score));
        self::Clear();
        return $rs ['id'];
    }

    public static function Edit($id, $pid, $name, $score)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET pid=?, name=?, score=?
				WHERE id=?
				", array($pid, $name, $score, $id));
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
    public static function GetNextIds($id, $self = false)
    {
        $rs = array();
        if ($self)
            $rs [] = $id;
        foreach (self::Instance()->Items() as $k => $v) {
            if ($v ['pid'] == $id)
                $rs [] = $k;
        }
        return $rs;
    }

    //得到子级(递归)ID列表
    public static function GetChildrenIds($id, $self = false)
    {
        $rs = array();
        if ($self)
            $rs [$id] = $id;

        $next = self::GetNextIds($id, $self);
        if (!empty($next)) {
            foreach ($next as $k => $v) {
                $rs[$v] = $v;
                $rs = array_merge($rs, self::GetChildrenIds($v, false));
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

    public static function Tree()
    {
        $rs = array();

        //根
        $rs = array(array('id' => '0', 'pid' => '0', 'name' => '外观评测项目', 'open' => true, 'icon' => '/img/ztree_croot.gif', 'type' => 1));

        $ru = self::Instance()->Items();
        foreach ($ru as $k => $v) {
            $rs [] = array('id' => $v ['id'], 'pid' => $v['pid'], 'name' => $v ['name'], 'open' => true, 'icon' => '/img/ztree_con.gif', 'type' => 2);
        }

        return $rs;
    }
}