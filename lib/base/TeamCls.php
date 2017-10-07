<?php
class TeamCls
{

	public static function isCanView($res, $uid)
	{
		$p = self::getUserPermission ( self::parseTeam ( $res ), $uid );
		return $p > 0 ? true : false;
	}

	public static function isCanAdd($res, $uid)
	{
		$p = self::getUserPermission ( self::parseTeam ( $res ), $uid );
		return $p > 1 ? true : false;
	}

	public static function isCanEdit($res, $uid)
	{
		$p = self::getUserPermission ( self::parseTeam ( $res ), $uid );
		return $p > 2 ? true : false;
	}

	public static function isCanDelete($res, $uid)
	{
		$p = self::getUserPermission ( self::parseTeam ( $res ), $uid );
		return $p > 3 ? true : false;
	}

	public static function getTeamList($res, $owner = array())
	{
		$rs = array ();
		
		foreach ( $owner as $k )
		{
			$rs [] = array ( 'user' => $k , 'username' => UserCls::Instance ()->GetUName ( $k ) , 'permission' => 5 , 'permissionname' => TeamPermissionCls::GetName ( 5 ) );
		}
		
		$team = self::parseTeam ( $res );
		
		foreach ( $team as $k => $v )
		{
			$rs [] = array ( 'user' => $k , 'username' => UserCls::Instance ()->GetUName ( $k ) , 'permission' => $v , 'permissionname' => TeamPermissionCls::GetName ( $v ) );
		}
		return $rs;
	}

	public static function AddUserToTeam($res, $uid, $permission)
	{
		$rs = self::parseTeam ( $res );
		if (array_key_exists ( $uid, $rs )) return false;
		$rs [$uid] = $permission;
		
		$team = '';
		$rx = array ();
		foreach ( $rs as $k => $v )
		{
			$rx [] = "$k|$v";
		}
		$team = implode ( ',', $rx );
		return $team;
	}

	public static function DeleteUserFromTeam($res, $uid)
	{
		$rs = self::parseTeam ( $res );
		if (array_key_exists ( $uid, $rs )) unset ( $rs [$uid] );
		
		$team = '';
		$rx = array ();
		foreach ( $rs as $k => $v )
		{
			$rx [] = "$k|$v";
		}
		$team = implode ( ',', $rx );
		return $team;
	}

	public static function mergerTeam($teams = array(), $creators = array())
	{
		$merger = '';
		
		foreach ( $creators as $creator )
		{
			$teams [] = "$creator|2";
		}
		
		$merger = implode ( ',', $teams );
		
		return $merger;
	}

	private static function parseTeam($res)
	{
		$rs = array ();
		
		$nodes = explode ( ',', $res );
		
		foreach ( $nodes as $v )
		{
			$node = explode ( '|', $v );
			
			if (isset ( $node [0] ) && UUID::Check ( $node [0] ))
			{
				if (isset ( $node [1] )) $node [1] = intval ( $node [1] );
				else $node [1] = 0;
				
				$rs [$node [0]] = $node [1];
			}
		}
		
		return $rs;
	}

	private static function getUserPermission($team, $uid)
	{
		if (array_key_exists ( $uid, $team )) return $team [$uid];
		return 0;
	}

	public static function getTeam($table, $id)
	{
		$rs = DB::db ()->Fetch ( "
				SELECT team
				FROM {$table}
				WHERE del=false AND id=?
				", array ( $id ) );
		if ($rs && count ( $rs ) > 0) return $rs ['team'];
		return '';
	}

	public static function setTeam($table, $id, $team)
	{
		DB::db ()->Query ( "
				UPDATE {$table}
				SET team=?
				WHERE id=? AND del=false
				", array ( $team , $id ) );
	}
}
?>