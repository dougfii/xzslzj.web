<?php
class ApproveAuditorCls
{
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
	}

	public static function Count($where = '')
	{
		$rs = DB::db ()->Fetch ( "
			SELECT COUNT(id) AS count
			FROM t_approve_auditor
			$where
			", array () );
		if ($rs && count ( $rs ) > 0 && $rs ['count'] > 0) return $rs ['count'];
		return 0;
	}

	public static function Items($where = '', $order = '', $limit = 20, $offset = 0)
	{
		$rs = DB::db ()->FetchAll ( "
			SELECT id, serial, sequenceid, auditorid, agree, memo, time
			FROM t_approve_auditor
			$where
			$order
			LIMIT ? OFFSET ?
			", array ( $limit , $offset ) );
		if ($rs && count ( $rs ) > 0)
		{
			for($i = 0; $i < count ( $rs ); $i ++)
			{
				$rs [$i] ['agreename'] = ApproveAgreeCls::GetName ( $rs [$i] ['agree'] );
			}
			return $rs;
		}
		return array ();
	}

	public static function Add($id, $approveid, $sequenceid, $auditorid, $serial)
	{
		DB::db ()->Query ( "
			INSERT INTO t_approve_auditor(id, approveid, sequenceid, auditorid, serial)
			VALUES(?,?,?,?,?)
			", array ( $id , $approveid , $sequenceid , $auditorid , $serial ) );
	}

	public static function getAuditorList($sequenceid)
	{
		$rs = DB::db ()->FetchAll ( "
				SELECT id, serial, approveid, sequenceid, auditorid, agree, memo, time
				FROM t_approve_auditor
				WHERE sequenceid=? AND del=false
				ORDER BY serial ASC
				", array ( $sequenceid ) );
		if ($rs && count ( $rs ) > 0)
		{
			for($i = 0; $i < count ( $rs ); $i ++)
			{
				$rs [$i] ['auditor'] = UserCls::Instance ()->GetUName ( $rs [$i] ['auditorid'] );
				$rs [$i] ['agreename'] = ApproveAgreeCls::GetName ( $rs [$i] ['agree'] );
			}
			
			return $rs;
		}
		
		return array ();
	}

	public static function processAuditor($sequenceid, $auditorid, $agree, $memo)
	{
		DB::db ()->Query ( "
			UPDATE t_approve_auditor
			SET agree=?, memo=?, time=CURRENT_TIMESTAMP
			WHERE sequenceid=? AND auditorid=? AND del=false
			", array ( $agree , $memo , $sequenceid , $auditorid ) );
	}

	public static function checkAuditorList($sequenceid)
	{
		$rs = DB::db ()->FetchAll ( "
				SELECT agree
				FROM t_approve_auditor
				WHERE sequenceid=? AND del=false
				ORDER BY serial ASC
				", array ( $sequenceid ) );
		if ($rs && count ( $rs ) > 0)
		{
			for($i = 0; $i < count ( $rs ); $i ++)
			{
				if ($rs [$i] ['agree'] == ApproveAgreeCls::UNKNOWN)
				{
					return ApproveAgreesCls::UNKNOWN;
				}
				elseif ($rs [$i] ['agree'] == ApproveAgreeCls::DISAGREE)
				{
					return ApproveAgreeCls::DISAGREE;
				}
			}
		}
		
		return ApproveAgreesCls::AGREE;
	}
}
?>