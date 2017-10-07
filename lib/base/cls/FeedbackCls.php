<?php

class FeedbackCls
{
    const TABLE = 't_feedback';
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
                $rs[$i]['user'] = UserCls::Instance()->Name($rs[$i]['uid']);
            }
            return $rs;
        }
        return array();
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

    public static function Add($project, $contacts, $phone, $email, $content)
    {
        $rs = DB::db()->Fetch("
                INSERT INTO " . self::TABLE . "(project, contacts, phone, email, content)
				VALUES(?,?,?,?,?)
				RETURNING id
				", array($project, $contacts, $phone, $email, $content));
        return $rs ['id'];
    }

    public static function Edit($id, $reply, $act, $uid)
    {
        DB::db()->Query("
                UPDATE " . self::TABLE . "
                SET reply=?, act=?, uid=?, replytime=CURRENT_TIMESTAMP
                WHERE id=?
                ", array($reply, $act, $uid, $id));
    }

    public static function Delete($id)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET del=true
				WHERE id=?
				", array($id));
    }
}