<?php

class LogSmsCls
{
    const TABLE = 't_log_sms';

    public static function Count($where = '')
    {
        $rs = DB::db()->Fetch("
				SELECT COUNT(id) AS count
				FROM " . self::TABLE . "
				$where
				", array());
        if ($rs && count($rs) > 0 && $rs ['count'] > 0) return $rs ['count'];
        return 0;
    }

    public static function Items($where = '', $order = '', $start = 0, $size = 20)
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
                $rs [$i] ['type'] = SmsTypeCls::GetName($rs [$i] ['tid']);
            }
            return $rs;
        }
        return array();
    }

    public static function Add($tid, $mobile, $valid, $expire)
    {
        DB::db()->Query("
				INSERT INTO " . self::TABLE . "(tid, mobile, valid, expire)
				VALUES(?,?,?,?)
				", array($tid, $mobile, $valid, $expire));
    }

    public static function GetLimitTypeDay($tid, $mobile)
    {
        $rs = DB::db()->Fetch("
				SELECT COUNT(id) AS n
				FROM " . self::TABLE . "
				WHERE tid=? AND mobile=? AND DATEDIFF(time, CURRENT_TIMESTAMP)=0
				", array($tid, $mobile));
        if ($rs && count($rs) > 0 && $rs ['n'] > 0) return $rs ['n'];
        return 0;
    }

    public static function IsValid($tid, $mobile, $valid)
    {
        $rs = DB::db()->FetchAll("
				SELECT id
				FROM " . self::TABLE . "
				WHERE tid=? AND mobile=? AND valid=? AND CURRENT_TIMESTAMP>=time AND CURRENT_TIMESTAMP<=expire
				ORDER BY id DESC
				LIMIT 1
				", array($tid, $mobile, $valid));
        if ($rs && count($rs) > 0) return true;
        return false;
    }
}