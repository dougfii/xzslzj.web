<?php
class LogSmsCls
{
	const TABLE = 't_log_sms';

	public static function Count($where = '')
	{
		$rs = DB::db ()->Fetch ( "
				SELECT COUNT(id) AS count
				FROM " . self::TABLE . "
				$where
				", array () );
		if ($rs && count ( $rs ) > 0 && $rs ['count'] > 0) return $rs ['count'];
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
		if ($rs && count ( $rs ) > 0) return $rs;
		return array ();
	}

	public static function Add($tid, $mobile, $message, $uid, $uaid, $ip, $code, $valid)
	{
		$rs = DB::db ()->Fetch ( "
				INSERT INTO " . self::TABLE . "(tid, mobile, message, uid, uaid, ip, code, valid)
				VALUES(?,?,?,?,?,?,?,?)
				RETURNING id
				", array ( $tid , $mobile , $message , $uid , $uaid , $ip , $code , $valid ) );
		return $rs ['id'];
	}
}
?>