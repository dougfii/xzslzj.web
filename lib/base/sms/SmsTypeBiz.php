<?php
class SmsTypeBiz
{
	const MESSAGE = 1;
	const JOIN = 2;
	const FORGET = 3;
	//
	private static $list = array ( 1 => '一般消息' , 2 => '会员注册' , 3 => '忘记密码' );

	public static function GetName($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id];
		return '';
	}

	public static function AllItems()
	{
		return self::$list;
	}
}
?>