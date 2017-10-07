<?php

class ArticleCls
{
    const TABLE = 't_article';
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

    public function Uid($id)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['uid'];
        return 0;
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
                $rs[$i]['type'] = ArticleTypeCls::Instance()->Name($rs[$i]['tid']);
                $rs[$i]['user'] = UserCls::Instance()->Name($rs[$i]['uid']);
            }
            return $rs;
        }

        return array();
    }


    public static function Add($tid, $uid, $pic, $title, $content, $html, $roll, $act, $time)
    {
        $rs = DB::db()->Fetch("
                INSERT INTO " . self::TABLE . "(tid, uid, pic, title, content, html, roll, act, time)
				VALUES(?,?,?,?,?,?,?,?,?)
				RETURNING id
				", array($tid, $uid, $pic, $title, $content, $html, $roll, $act, $time));
        return $rs ['id'];
    }

    public static function Edit($id, $tid, $uid, $pic, $title, $content, $html, $roll, $act, $last, $time)
    {
        DB::db()->Query("
                UPDATE " . self::TABLE . "
                SET tid=?, uid=?, pic=?, title=?, content=?, html=?, roll=?, act=?, last=?, time=?
                WHERE id=?
                ", array($tid, $uid, $pic, $title, $content, $html, $roll, $act, $last, $time, $id));
    }

    public static function Delete($id)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET del=true
				WHERE id=?
				", array($id));
    }

    public static function IncViews($id)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET views=views+1
				WHERE id=?
				", array($id));
    }
}