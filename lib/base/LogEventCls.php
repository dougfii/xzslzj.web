<?php

//momo
class LogEventCls
{
    const TABLE = 't_log_event';

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
        if ($rs && count($rs) > 0)
        {
            for ($i = 0; $i < count($rs); $i++)
            {
                $rs [$i] ['event'] = nl2br($rs [$i] ['event']);
                $rs [$i] ['memory'] = Format::BytesString($rs [$i] ['memory']);
            }
            return $rs;
        }
        return array();
    }
}

?>