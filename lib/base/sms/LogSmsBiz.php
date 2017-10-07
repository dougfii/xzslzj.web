<?php
class LogSmsBiz
{

	public static function Add($tid, $mobile, $message, $uid, $uaid, $ip, $code, $valid)
	{
		return LogSmsCls::Add ( $tid, $mobile, $message, $uid, $uaid, $ip, $code, $valid );
	}

	public static function GetJoin($tid, $uaid, $ip, $mobile, $code)
	{
		$rs = LogSmsCls::Items ( "WHERE tid={$tid} AND uaid='{$uaid}' AND ip={$ip} AND mobile='{$mobile}' AND CURRENT_TIMESTAMP>=time AND CURRENT_TIMESTAMP<=valid", "ORDER BY id DESC", 0, 0 );
		if ($rs && count ( $rs ) > 0)
		{
			for($i = 0; $i < count ( $rs ); $i ++)
			{
				if ($rs [$i] ['code'] == $code) return true;
			}
			return false;
		}
		return null;
	}

	public static function GetLimitDayTypeIP($tid, $uaid, $ip)
	{
		return LogSmsCls::Count ( "WHERE tid={$tid} AND uaid='{$uaid}' AND ip={$ip} AND DATE_TRUNC('day', time)=DATE_TRUNC('day', CURRENT_TIMESTAMP)" );
	}
}
?>