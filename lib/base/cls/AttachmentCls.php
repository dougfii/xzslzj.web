<?php

class AttachmentCls
{
    const TABLE = 't_attachment';
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
    }

    public function Item($id)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id];
        return array();
    }

    private function LoadItem($id)
    {
        if (!array_key_exists($id, self::$list)) {
            $rs = self::Items("WHERE del=false AND id={$id}", '', 0, 1);
            if ($rs && count($rs) > 0)
                self::$list [$rs [0] ['id']] = $rs [0];
        }
    }

    public static function Count($where = '')
    {
        $rs = DB::db()->Fetch("
				SELECT COUNT(id) AS count
				FROM " . self::TABLE . "
				$where
				", array());
        if ($rs && count($rs) > 0 && $rs ['count'] > 0)
            return $rs ['count'];
        return 0;
    }

    public static function Items($where, $order, $start, $size)
    {
        $limit = DB::db()->Limit($start, $size);
        $rs = DB::db()->FetchAll("
				SELECT *
				FROM " . self::TABLE . "
				$where
				$order
				$limit
				");
        return $rs;
    }

    public static function Results($where, $order, $page, $size)
    {
        $where = 'WHERE del=false ' . $where;
        $order = empty($order) ? 'ORDER BY id DESC' : $order;

        $count = self::Count($where);
        $page = HTML::PageNum($count, $page, $size);
        $start = HTML::PagePos($count, $page, $size);
        $rs = self::Items($where, $order, $start, $size);

        return array($count, $rs);
    }

    public static function Add($pid, $tid, $no, $name, $url, $file, $ext, $size)
    {
        $rs = DB::db()->Fetch("
                INSERT INTO " . self::TABLE . "(pid, tid, no, name, url, file, ext, size)
				VALUES(?,?,?,?,?,?,?,?)
				RETURNING id
				", array($pid, $tid, $no, $name, $url, $file, $ext, $size));
        return $rs ['id'];
    }

    public static function Edit($id, $pid, $tid, $no, $name, $url, $file, $ext, $size)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET pid=?, tid=?, no=?, name=?, url=?, file=?, ext=?, size=?
				WHERE id=?
				", array($pid, $tid, $no, $name, $url, $file, $ext, $size, $id));
    }

    public static function EditFixed($pid, $tid, $no, $name, $url, $file, $ext, $size)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET name=?, url=?, file=?, ext=?, size=?
				WHERE pid=? AND tid=? AND no=?
				", array($name, $url, $file, $ext, $size, $pid, $tid, $no));
    }

    public static function AddFixed($pid, $tid, $no, $name, $url, $file, $ext, $size)
    {
        $rs = self::GetFixedItem($pid, $tid, $no);
        if (!empty($rs)) {
            self::EditFixed($pid, $tid, $no, $name, $url, $file, $ext, $size);
        } else {
            self::Add($pid, $tid, $no, $name, $url, $file, $ext, $size);
        }
    }

    public static function Delete($id)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET del=true
				WHERE id=?
				", array($id));
    }

    public static function SetAct($id)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET act=NOT act
				WHERE id=?
				", array($id));
    }

    public static function GetFixedItems($pid, $tid)
    {
        $rs = self::Items("WHERE del=false AND pid={$pid} AND tid={$tid}", 'ORDER BY no ASC, id ASC', 0, 0);
        return $rs;
    }

    public static function GetFixedItem($pid, $tid, $no)
    {
        $rs = self::Items("WHERE del=false AND pid={$pid} AND tid={$tid} AND no={$no}", 'ORDER BY no ASC, id ASC', 0, 1);
        return ($rs && count($rs) > 0) ? $rs[0] : array();
    }

    public static function GetDynamicItems($pid, $tid)
    {
        $rs = self::Items("WHERE del=false AND pid={$pid} AND tid={$tid}", 'ORDER BY no ASC, id ASC', 0, 0);
        return $rs;
    }
}