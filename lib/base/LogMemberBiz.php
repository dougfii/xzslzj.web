<?php
class LogMemberBiz
{

	public static function Add($mid, $ua, $ip)
	{
		return LogMemberCls::Add ( $mid, $ua, $ip );
	}
}
?>