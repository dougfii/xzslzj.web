<?php

class ApproveCauseCls
{
	const EXECUTE = 1;
	const CLOSE = 10;
	const FINISHED = 20;
	private static $map = array ( 1 => '执行' , 10 => '关闭' , 20 => '结束' );

	public static function AllItems()
	{
		return self::$map;
	}

	public static function GetName($id)
	{
		if (array_key_exists ( $id, self::$map ))
		{
			return self::$map [$id];
		}
		return false;
	}
}
?>