<?php
class UtilMod extends BaseMod
{
	const MESSAGE_FORBIDDEN_EDIT = '当前状态禁止编辑';
	const MESSAGE_UNABLE_LIST = '没有列表权限';
	const MESSAGE_UNABLE_VIEW = '没有浏览权限';
	const MESSAGE_UNABLE_ADD = '没有增加权限';
	const MESSAGE_UNABLE_EDIT = '没有编辑权限';
	const MESSAGE_UNABLE_DELETE = '没有删除权限';
	const MESSAGE_UNABLE_EXPORT = '没有导出权限';
	const MESSAGE_UNABLE_CONVERT = '没有转换权限';
	const MESSAGE_UNABLE_TEAM = '没有团队权限';
	const MESSAGE_UNABLE_COST = '没有成本权限';

	public function index()
	{
	}

	public static function MessageForbiddenEdit()
	{
		echo self::MESSAGE_FORBIDDEN_EDIT;
	}

	public static function MessageUnableAdd()
	{
		echo self::MESSAGE_UNABLE_ADD;
	}

	public static function MessageUnableEdit()
	{
		echo self::MESSAGE_UNABLE_EDIT;
	}

	public static function MessageUnableDelete()
	{
		echo self::MESSAGE_UNABLE_DELETE;
	}

	public static function MessageUnableTeam()
	{
		echo self::MESSAGE_UNABLE_TEAM;
	}

	public static function MessageUnableCost()
	{
		echo self::MESSAGE_UNABLE_COST;
	}

	public static function MessageBoxForbiddenEdit()
	{
		self::MessageBox ( self::MESSAGE_FORBIDDEN_EDIT );
	}

	public static function MessageBoxUnableList()
	{
		self::MessageBox ( self::MESSAGE_UNABLE_LIST );
	}

	public static function MessageBoxUnableView()
	{
		self::MessageBox ( self::MESSAGE_UNABLE_VIEW );
	}

	public static function MessageBoxUnableAdd()
	{
		self::MessageBox ( self::MESSAGE_UNABLE_ADD );
	}

	public static function MessageBoxUnableEdit()
	{
		self::MessageBox ( self::MESSAGE_UNABLE_EDIT );
	}

	public static function MessageBoxUnableExport()
	{
		self::MessageBox ( self::MESSAGE_UNABLE_EXPORT );
	}

	public static function MessageBoxUnableConvert()
	{
		self::MessageBox ( self::MESSAGE_UNABLE_CONVERT );
	}

	private static function MessageBox($msg)
	{
		echo '<div class="alert alert-error text-center">' . $msg . '</div><div class="btn-panel"><button class="btn" onclick="history.back()">返回</button></div>';
	}
	
	// 隐藏功能
	public function InitPermission()
	{
		echo '开始初始化权限!<br />';
		
		$code = Url::Req ( 'code', '', 'str' );
		if ($code == '5608121')
		{
			$this->InitUserPermission ();
			$this->InitAdminPermission ();
			$this->InitDepartmentPermission ();
		}
		else
		{
			echo '无法初始化权限!<br />';
		}
		
		echo '初始化权限完成!<br />';
	}

	public function InitAdminPermission()
	{
		echo '开始初始化管理员权限!<br />';
		
		$code = Url::Req ( 'code', '', 'str' );
		if ($code == '5608121')
		{
			$p = P::InitPermissionStrings ( true );
			
			UserCls::EditPermission ( md5 ( '0' ), $p );
			
			echo '管理员权限初始化完成!<br />';
		}
		else
		{
			echo '无法初始化管理员权限!<br />';
		}
	}

	public function InitUserPermission()
	{
		echo '开始初始化用户权限!<br />';
		
		$code = Url::Req ( 'code', '', 'str' );
		if ($code == '5608121')
		{
			$p = P::InitPermissionStrings ();
			
			UserCls::InitPermission ( $p );
			
			echo '用户权限初始化完成!<br />';
		}
		else
		{
			echo '无法初始化用户权限!<br />';
		}
	}

	public function InitDepartmentPermission()
	{
		echo '开始初始化部门权限!<br />';
		
		$code = Url::Req ( 'code', '', 'str' );
		if ($code == '5608121')
		{
			$p = P::InitPermissionStrings ();
			
			DepartmentCls::InitPermission ( $p );
			
			echo '部门权限初始化完成!<br />';
		}
		else
		{
			echo '无法初始化部门权限!<br />';
		}
	}
}
?>