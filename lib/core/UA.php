<?php
class UA
{

	public static function GetUA()
	{
		return $_SERVER ['HTTP_USER_AGENT'];
	}

	public static function GetUAID()
	{
		return md5 ( self::GetUA () );
	}
}
?>