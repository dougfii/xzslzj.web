<?php
class ApproveCls
{
	protected static $cache = 't_approve';
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

	public function getApproveObjectID($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['approveobjectid'];
		return 0;
	}

	public function getApproveObject($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['approveobject'];
		return '';
	}

	public function getMethod($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['method'];
		return '';
	}

	public function getFlow($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['flow'];
		return array ();
	}

	public function getProcess($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['process'];
		return '';
	}

	public function GetAct($id)
	{
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['act'];
		return array ();
	}

	public function getIDByApproveObjectID($approveobjectid, $cause)
	{
		foreach ( self::$list as $v )
		{
			if ($v ['approveobjectid'] == $approveobjectid && $v ['cause'] == $cause) return $v ['id'];
		}
		return '';
	}

	public function getProcessByApproveObjectID($approveobjectid, $cause)
	{
		foreach ( self::$list as $v )
		{
			if ($v ['approveobjectid'] == $approveobjectid && $v ['cause'] == $cause) return self::ApproveRuntimeProcess ( $v ['process'] );
		}
		return '';
	}

	public function AllItems()
	{
		return self::$list;
	}

	public static function existApproveObjectIDAndCause($approveobjectid, $cause, $selfid = null)
	{
		if (empty ( $selfid ))
		{
			$rs = DB::db ()->Fetch ( "
					SELECT COUNT(id) AS count
					FROM t_approve_list
					WHERE approveobjectid=? AND cause=? AND del=false
					LIMIT 1
					", array ( $approveobjectid , $cause ) );
		}
		else
		{
			$rs = DB::db ()->Fetch ( "
					SELECT COUNT(id) AS count
					FROM t_approve_list
					WHERE approveobjectid=? AND cause=? AND id!=? AND del=false
					", array ( $approveobjectid , $cause , $selfid ) );
		}
		if ($rs && count ( $rs ) > 0 && $rs ['count'] > 0) return true;
		return false;
	}

	public static function Count($where = '')
	{
		$rs = DB::db ()->Fetch ( "
			SELECT COUNT(id) AS count
			FROM t_approve_list
			$where
			", array () );
		if ($rs && count ( $rs ) > 0 && $rs ['count'] > 0) return $rs ['count'];
		return 0;
	}

	public static function Items($where = '', $order = '', $limit = 20, $offset = 0)
	{
		$rs = DB::db ()->FetchAll ( "
			SELECT id, approveobjectid, cause, method, flow, process, act, time
			FROM t_approve_list
			$where
			$order
			LIMIT ? OFFSET ?
			", array ( $limit , $offset ) );
		if ($rs && count ( $rs ) > 0)
		{
			for($i = 0; $i < count ( $rs ); $i ++)
			{
				$rs [$i] ['approveobject'] = ApproveObjectCls::GetName ( $rs [$i] ['approveobjectid'] );
				$rs [$i] ['causename'] = ApproveCauseCls::GetName ( $rs [$i] ['cause'] );
				$rs [$i] ['method'] = Util::Bool ( $rs [$i] ['method'] );
				$rs [$i] ['act'] = Util::Bool ( $rs [$i] ['act'] );
			}
			return $rs;
		}
		return array ();
	}

	public static function Add( $gid, $departmentid, $act, $approveobjectid, $cause, $pu, $flow )
	{
		$rs = DB::db ()->Query ( "
				INSERT INTO t_approve_list(id, approveobjectid, cause, method, flow, process, act)
				VALUES(?,?,?,?,?,?,?)
				RETURNING id
				", array ( $gid, $departmentid, $act, $approveobjectid, $cause, $pu, $flow ) );
		return $rs['id'];
		
		self::Clear ();
	}

	public static function Edit($id, $approveobjectid, $cause, $method, $flow, $process, $act)
	{
		DB::db ()->Query ( "
				UPDATE t_approve_list
				SET approveobjectid=?, cause=?, method=?, flow=?, process=?, act=?
				WHERE id=? AND del=false
				", array ( $approveobjectid , $cause , $method , $flow , $process , $act , $id ) );
		
		self::Clear ();
	}

	public static function Delete($id)
	{
		DB::db ()->Query ( "
			UPDATE t_approve_list
			SET del=true
			WHERE id=?
			", array ( $id ) );
		
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
			$list [$v ['id']] ['approveobject'] = ApproveObjectCls::GetName ( $v ['approveobjectid'] );
			$list [$v ['id']] ['causename'] = ApproveCauseCls::GetName ( $v ['cause'] );
			$list [$v ['id']] ['method'] = Util::Bool ( $list [$v ['id']] ['method'] );
			$list [$v ['id']] ['act'] = Util::Bool ( $list [$v ['id']] ['act'] );
		}
		
		$cache = new Cache ();
		$cache->Set ( $list, 'system', self::$cache );
		
		self::$list = $list;
	}

	protected function loadDB()
	{
		$rs = DB::db ()->FetchAll ( "
			SELECT id, approveobjectid, cause, method, flow, process, act, time
			FROM t_approve_list
			WHERE del=false
			ORDER BY time ASC
			" );
		if ($rs && count ( $rs ) > 0) return $rs;
		return array ();
	}

	public static function Clear()
	{
		$cache = new Cache ();
		$cache->Delete ( 'system', self::$cache );
	}

	public static function ApproveRuntimeProcess($process)
	{
		$conditons = explode ( ';', $process );
		if (! empty ( $conditons ))
		{
			for($i = 0; $i < count ( $conditons ); $i ++)
			{
				$condtion = trim ( $conditons [$i] );
				
				if (preg_match ( '/^\$c\d*\s*\=\s*\(\s*\$rd\[\'(creatordepartmentid|ownerdepartmentid|salesdepartmentid)\'\]\s*(\=\=|\!\=)\s*\'\w{8}\-\w{4}\-\w{4}-\w{4}\-\w{12}\'\s*\)$/i', $condtion ))
				{
					$count = preg_match_all ( '/^\$c(\d*)\s*\=\s*\(\s*\$rd\[\'(creatordepartmentid|ownerdepartmentid|salesdepartmentid)\'\]\s*(\=\=|\!\=)\s*\'(\w{8}\-\w{4}\-\w{4}-\w{4}\-\w{12})\'\s*\)$/i', $condtion, $matchs, PREG_SET_ORDER );
					if ($count !== false && $count = 1 && count ( $matchs [0] ) == 5)
					{
						$order = $matchs [0] [1];
						$fieldname = strtolower ( $matchs [0] [2] );
						$flag = $matchs [0] [3];
						$departmentid = $matchs [0] [4];
						$relation = ' || ';
						
						if ($flag == '==')
						{
							$relation = ' || ';
						}
						elseif ($flag == '!=')
						{
							$relation = ' && ';
						}
						else
						{
							throw new ApproveRuntimeException ( '审批符号运行时错误' );
						}
						
						if ($fieldname == 'creatordepartmentid')
						{
							$fieldname = 'creatorid';
						}
						elseif ($fieldname == 'ownerdepartmentid')
						{
							$fieldname = 'ownerid';
						}
						elseif ($fieldname == 'salesdepartmentid')
						{
							$fieldname = 'salesid';
						}
						else
						{
							throw new ApproveRuntimeException ( '审批字段运行时错误' );
						}
						
						$users = UserCls::Instance ()->getUserIDListByDepartmentID ( $departmentid );
						if (Util::IsBlankArray ( $users ))
						{
							$condtion = '';
						}
						else
						{
							$users = "(\$rd['{$fieldname}']{$flag}'" . implode ( "' {$relation} \$rd['{$fieldname}']{$flag}'", $users ) . "')";
							$condtion = "\$c{$order}=" . $users;
						}
					}
					$conditons [$i] = $condtion;
				}
			}
			
			$process = implode ( ';', $conditons );
		}
		
		return $process;
	}
}
class ApproveRuntimeException extends Exception
{
}
?>