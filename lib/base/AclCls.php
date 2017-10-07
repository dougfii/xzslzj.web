<?php

class AclCls
{
	private $uid = null;
	private static $instance = null;

	public static function Instance($uid)
	{
		if (self::$instance == null)
		{
			self::$instance = new self ( $uid );
		}
		return self::$instance;
	}

	private function __construct($uid)
	{
		$this->uid = $uid;
	}

	public function getRoleResourceViewUserList($res)
	{
		$users = array ();
		
		$roles = RoleCls::Instance ()->getSUserPermissionItems ( $this->uid );

		foreach ( $roles as $v )
		{
			$permissions = explode ( ',', $v ['permission'] );
			if ($res < count ( $permissions ) && P::isView ( $permissions [$res] ))
			{
				$users = array_merge ( $users, explode ( ',', $v ['tuser'] ) );
			}
		}
		return $users;
	}

	public function getRoleResourceViewOrgList($res)
	{
		$orgs = array ();
		
		$roles = RoleCls::Instance ()->getSUserPermissionItems ( $this->uid );
		
		foreach ( $roles as $v )
		{
			$permissions = explode ( ',', $v ['permission'] );
			if ($res < count ( $permissions ) && P::isView ( $permissions [$res] ))
			{
				$orgs = array_merge ( $orgs, explode ( ',', $v ['tuser'] ) );
			}
		}
		
		return $orgs;
	}

	public function getRoleResourceViewGroupList($res)
	{
		$groups = array ();

		$roles = RoleCls::Instance ()->getSUserPermissionItems ( $this->uid );

		foreach ( $roles as $v )
		{
			$permissions = explode ( ',', $v ['permission'] );
			if ($res < count ( $permissions ) && P::isView ( $permissions [$res] ))
			{
				$groups = array_merge ( $groups, explode ( ',', $v ['tuser'] ) );
			}
		}

		return $groups;
	}
}
?>