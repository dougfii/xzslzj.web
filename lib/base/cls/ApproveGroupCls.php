<?php
class ApproveGroupCls
{
	protected static $cache = 'approve_group';
	protected static $list = array ();
	private static $instance = null;

	public static function Instance()
	{
		if (self::$instance == null)
		{
			self::$instance = new self ();
		}
		return self::$instance;
	}

	private function __construct()
	{
		$this->load ();
	}

	public function Item($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id];
		return array ();
	}

	public function getApproveID($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['approveid'];
		return null;
	}

	public function getGroups($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['groups'];
		return 0;
	}

	public function GetSerial($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['serial'];
		return 0;
	}

	public function getAuditorID($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['auditorid'];
		return null;
	}

	public function AllItems()
	{
		return self::$list;
	}

	public function getGroupList($approveid)
	{
		$rs = array ();
		foreach ( self::$list as $v )
		{
			if ($v ['approveid'] == $approveid)
			{
				if (! array_key_exists ( $v ['groups'], $rs ))
				{
					$rs [$v ['groups']] = array ();
				}
				
				$rs [$v ['groups']] [$v ['serial']] = $v ['auditorid'];
			}
		}
		return $rs;
	}

	public static function Count($where = '')
	{
		$rs = DB::db ()->Fetch ( "
			SELECT COUNT(id) AS count
			FROM t_approve_group
			$where
			", array () );
		if ($rs && count ( $rs ) > 0 && $rs ['count'] > 0) return $rs ['count'];
		return 0;
	}

	public static function Items($where = '', $order = '', $limit = 20, $offset = 0)
	{
		$rs = DB::db ()->FetchAll ( "
			SELECT id, approveid, groups, serial, auditorid, time
			FROM t_approve_group
			$where
			$order
			LIMIT ? OFFSET ?
			", array ( $limit , $offset ) );
		if ($rs && count ( $rs ) > 0)
		{
			return $rs;
		}
		return array ();
	}

	public static function Add($id, $approveid, $groups, $serial, $auditorid)
	{
		DB::db ()->Query ( "
				INSERT INTO t_approve_group(id, approveid, groups, serial, auditorid)
				VALUES(?,?,?,?,?)
				", array ( $id , $approveid , $groups , $serial , $auditorid ) );
		
		self::Clear ();
	}

	public static function Edit($id, $approveid, $groups, $serial, $auditorid)
	{
		DB::db ()->Query ( "
				UPDATE t_approve_group
				SET approveid=?, groups=?, serial=?, auditorid=?
				WHERE id=? AND del=false
				", array ( $approveid , $groups , $serial , $auditorid , $id ) );
		
		self::Clear ();
	}

	public static function Delete($id)
	{
		DB::db ()->Query ( "
			UPDATE t_approve_group
			SET del=true
			WHERE id=?
			", array ( $id ) );
		
		self::Clear ();
	}

	public static function DeleteGroupList($approveid)
	{
		DB::db ()->Query ( "
			UPDATE t_approve_group
			SET del=true
			WHERE approveid=?
			", array ( $approveid ) );
		
		self::Clear ();
	}

	protected function load()
	{
		if (! $this->loadCache ()) $this->loadData ();
	}

	protected function loadCache()
	{
		$cache = new Cache ();
		$list = $cache->get ( 'system', self::$cache );
		
		if ($list !== false && is_array ( $list ))
		{
			self::$list = $list;
			return true;
		}
		return false;
	}

	protected function loadData()
	{
		$list = array ();
		$rs = $this->loadDB ();
		
		foreach ( $rs as $k => $v )
		{
			$list [$v ['id']] = $v;
		}
		
		$cache = new Cache ();
		$cache->Set ( $list, 'system', self::$cache );
		
		self::$list = $list;
	}

	protected function loadDB()
	{
		$rs = DB::db ()->FetchAll ( "
			SELECT id, approveid, groups, serial, auditorid, time
			FROM t_approve_group
			WHERE del=false
			ORDER BY time ASC, groups ASC, serial ASC
			" );
		if ($rs && count ( $rs ) > 0) return $rs;
		return array ();
	}

	public static function Clear()
	{
		$cache = new Cache ();
		$cache->Delete ( 'system', self::$cache );
	}
}
?>