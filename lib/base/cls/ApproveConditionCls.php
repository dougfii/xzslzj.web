<?php
class ApproveConditionCls
{
	protected static $cache = 'approve_condition';
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

	public function GetSerial($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['serial'];
		return 0;
	}

	public function getField($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['field'];
		return '';
	}

	public function GetName($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['name'];
		return '';
	}

	public function getType($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['type'];
		return '';
	}

	public function getOperator($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['operator'];
		return '';
	}

	public function getValue($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['value'];
		return '';
	}

	public function getRealvalue($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['realvalue'];
		return '';
	}

	public function AllItems()
	{
		return self::$list;
	}

	public function getConditionList($approveid)
	{
		$rs = array ();
		foreach ( self::$list as $v )
		{
			if ($v ['approveid'] == $approveid) $rs [$v ['serial']] = $v;
		}
		return $rs;
	}

	public static function Count($where = '')
	{
		$rs = DB::db ()->Fetch ( "
			SELECT COUNT(id) AS count
			FROM t_approve_condition
			$where
			", array () );
		if ($rs && count ( $rs ) > 0 && $rs ['count'] > 0) return $rs ['count'];
		return 0;
	}

	public static function Items($where = '', $order = '', $limit = 20, $offset = 0)
	{
		$rs = DB::db ()->FetchAll ( "
				SELECT id, approveid, serial, field, name, type, operator, value, realvalue
				FROM t_approve_condition
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

	public static function Add($id, $approveid, $serial, $field, $name, $type, $operator, $value, $realvalue)
	{
		DB::db ()->Query ( "
				INSERT INTO t_approve_condition(id, approveid, serial, field, name, type, operator, value, realvalue)
				VALUES(?,?,?,?,?,?,?,?,?)
				", array ( $id , $approveid , $serial , $field , $name , $type , $operator , $value , $realvalue ) );
		
		self::Clear ();
	}

	public static function Edit($id, $approveid, $serial, $field, $name, $type, $operator, $value, $realvalue)
	{
		DB::db ()->Query ( "
				UPDATE t_approve_condition
				SET approveid=?, serial=?, field=?, name=?, type=?, operator=?, value=?, realvalue=?
				WHERE id=? AND del=false
				", array ( $approveid , $serial , $field , $name , $type , $operator , $value , $realvalue , $id ) );
		
		self::Clear ();
	}

	public static function Delete($id)
	{
		DB::db ()->Query ( "
			UPDATE t_approve_condition
			SET del=true
			WHERE id=?
			", array ( $id ) );
		
		self::Clear ();
	}

	public static function DeleteConditionList($approveid)
	{
		DB::db ()->Query ( "
			UPDATE t_approve_condition
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
			SELECT id, approveid, serial, field, name, type, operator, value, realvalue
			FROM t_approve_condition
			WHERE del=false
			ORDER BY time ASC, serial ASC
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