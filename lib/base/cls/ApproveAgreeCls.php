<?php
class ApproveAgreeCls
{
	const UNKNOWN = 1;
	const DISAGREE = 2;
	const AGREE = 3;
	//
	private static $map = array ( 1 => '未批' , 2 => '不同意' , 3 => '同意' );

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