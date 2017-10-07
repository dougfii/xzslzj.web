<?php
class LogMemberCls
{
	const TABLE = 't_log_member';

	public static function Count($where = '')
	{
		$rs = DB::db ()->Fetch ( "
				SELECT COUNT(id) AS count
				FROM " . self::TABLE . "
				$where
				", array () );
		if ($rs && count ( $rs ) > 0) return $rs ['count'];
		return 0;
	}

	public static function Items($where = '', $order = '', $start = 0, $size = 20)
	{
		$limit = DB::db ()->Limit ( $start, $size );
		$rs = DB::db ()->FetchAll ( "
				SELECT *
				FROM " . self::TABLE . "
				$where
				$order
				$limit
				" );
		if ($rs && count ( $rs ) > 0)
		{
			for($i = 0; $i < count ( $rs ); $i ++)
			{
				$rs [$i] ['member'] = MemberBiz::GetName ( $rs [$i] ['mid'] );
				$rs [$i] ['ip'] = long2ip ( $rs [$i] ['ip'] );
			}
			return $rs;
		}
		return array ();
	}

	public static function Add($mid, $ua, $ip)
	{
		$rs = DB::db ()->Fetch ( "
				INSERT INTO " . self::TABLE . "(mid, ua, ip)
				VALUES(?,?,?)
				RETURNING id
				", array ( $mid , $ua , $ip ) );
		return $rs ['id'];
	}
}
?>