<?php
class FinishedStateCls
{
	const INIT = 0; // 初始状态
	const UNFINISHED = 100; // 未结束
	const RETREAT = 200; // 审退
	const APPROVE = 300; // 审批中
	const FINISHED = 500; // 结束
	                      
	//
	private static $map = array ( 	//
	0 => '--' , 	//
	100 => '未结束' , 	//
	200 => '审退' , 	//
	300 => '审批中' , 	//
	500 => '结束' );

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

	public static function IsInit($id)
	{
		return $id === self::INIT;
	}

	public static function IsUnfinished($id)
	{
		return $id === self::UNFINISHED || $id === self::RETREAT;
	}

	public static function IsApprove($id)
	{
		return $id === self::APPROVE;
	}

	public static function IsExecute($id)
	{
		return $id < self::APPROVE;
	}

	public static function IsFinished($id)
	{
		return $id === self::FINISHED;
	}
}
?>