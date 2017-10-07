<?php
class StateCls
{
	// ***** 可修改部分 *****
	//
	const NEWADD = 0;
	const MODIFY = 100;
	const FAILED = 200;
	
	// ***** 不可修改部分 *****
	// 不可修改临时状态
	const APPROVE = 300;
	// 非成功关闭
	const CLOSE = 400;
	// 成功关闭
	const SUCCESS = 500;
	private static $map = array ( 	//
	0 => '--' , 	//
	100 => '修改' , 	//
	200 => '审退' , 	//
	300 => '审批中' , 	//
	400 => '关闭' , 	//
	500 => '成功' );

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

	public static function isApprove($id)
	{
		return $id === self::APPROVE;
	}

	public static function isExecute($id)
	{
		return $id < self::APPROVE;
	}

	public static function isSuccess($id)
	{
		return $id === self::SUCCESS;
	}

	public static function isCanDisplay($id)
	{
		return $id <= self::APPROVE;
	}
}
?>