<?php

class MsgCls
{
    const TABLE = 't_msg';
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

    public function Pid($id)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['pid'];
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
        if ($rs && count($rs) > 0) return $rs;
        return array();
    }

    public static function Add($type, $direct, $senderid, $receiverid, $sender, $receiver, $nodeid, $objid, $msg)
    {
        $rs = DB::db()->Fetch("
                INSERT INTO " . self::TABLE . "(type, direct, senderid, receiverid, sender, receiver, nodeid, objid, msg)
				VALUES(?,?,?,?,?,?,?,?,?)
				RETURNING id
				", array($type, $direct, $senderid, $receiverid, $sender, $receiver, $nodeid, $objid, $msg));

        return $rs ['id'];
    }

    public static function Delete($id)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET del=true
				WHERE id=?
				", array($id));
    }

    public static function SetRead($id)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET read=NOT read
				WHERE id=?
				", array($id));
    }

    public static function GetProjectUnread($id)
    {
        $rs = self::Items("WHERE del=false AND read=false AND direct=" . MsgDirectCls::FROM_USER . " AND receiverid=" . $id, 'ORDER BY id DESC', 0, 0);
        return ($rs && count($rs) > 0) ? $rs : array();
    }

    public static function GetUserUnread()
    {
        $rs = self::Items("WHERE del=false AND read=false AND direct=" . MsgDirectCls::FROM_PROJECT, 'ORDER BY id DESC', 0, 0);
        return ($rs && count($rs) > 0) ? $rs : array();
    }

}