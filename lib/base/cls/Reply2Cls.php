<?php

class Reply2Cls
{
    const TABLE = 't_reply2';
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
        if ($rs && count($rs) > 0) {
            for ($i = 0; $i < count($rs); $i++) {
                $rs[$i]['project'] = ProjectCls::Instance()->Name($rs[$i]['pid']);
                $rs[$i]['user'] = UserCls::Instance()->Name($rs[$i]['uid']);
            }
            return $rs;
        }

        return array();
    }


    public static function Add($pid, $fid, $no, $content, $date, $uid, $act)
    {
        $rs = DB::db()->Fetch("
                INSERT INTO " . self::TABLE . "(pid, fid, no, content, date, uid, act)
				VALUES(?,?,?,?,?,?,?)
				RETURNING id
				", array($pid, $fid, $no, $content, $date, $uid, $act));
        return $rs ['id'];
    }

    public static function GetLastItem($pid, $fid)
    {
        $rs = self::Items("WHERE del=false AND pid={$pid} AND fid={$fid}", 'ORDER BY id DESC', 0, 1);
        return ($rs && count($rs) > 0) ? $rs[0] : array();
    }
}