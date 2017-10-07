<?php

//momo
class LogLoginCls
{
    const TABLE = 't_log_login';

    public static function Count($where)
    {
        $rs = DB::db()->Fetch("
				SELECT COUNT(id) AS count
				FROM " . self::TABLE . "
				$where
				", array());
        if ($rs && count($rs) > 0)
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
                $rs [$i] ['type'] = $rs[$i]['tid'] == 1 ? '会员' : ($rs[$i]['tid'] == 2 ? '用户' : '');
                $rs [$i] ['user'] = $rs[$i]['tid'] == 1 ? ProjectCls::Instance()->Name($rs[$i]['uid']) : ($rs[$i]['tid'] == 2 ? UserCls::Instance()->Username($rs[$i]['uid']) : '');
                $rs [$i] ['ip'] = long2ip($rs [$i] ['ip']);
            }
            return $rs;
        }
        return array();
    }

    public static function Results($where, $order, $page, $size)
    {
        $where = 'WHERE 1=1 ' . $where;
        $order = empty($order) ? 'ORDER BY id DESC' : $order;

        $count = self::Count($where);
        $page = HTML::PageNum($count, $page, $size);
        $start = HTML::PagePos($count, $page, $size);
        $rs = self::Items($where, $order, $start, $size);

        return array($count, $rs);
    }

    public static function Add($tid, $uid, $ip)
    {
        DB::db()->Query("
				INSERT INTO " . self::TABLE . "(tid, uid, ip)
				VALUES(?,?,?)
				", array($tid, $uid, $ip));
    }
}

?>