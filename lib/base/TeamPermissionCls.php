<?php
class TeamPermissionCls
{
	const NOT = 0;
	const VIEW = 1;
	const ADD = 2;
	const EDIT = 3;
	const DELETE = 4;
	const ALL = 5;
	//
	private static $list = array ( 0 => '无' , 1 => '浏览' , 2 => '增加' , 3 => '编辑' , 4 => '删除' , 5 => '完全' );

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