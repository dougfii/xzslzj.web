<?php
class UploadCls
{
	const TABLE = 't_upload';
	const TABLEHR = 't_hr_upload';
	const TABLEHRFRM = 't_hr_filereleasemanage';
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

	public function Item($id)
	{
		$this->LoadItem ( $id );
		if (array_key_exists ( $id, self::$list )) return self::$list [$id];
		return array ();
	}

	public function GetName($id)
	{
		$this->LoadItem ( $id );
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['name'];
		return '';
	}

	public function GetAct($id)
	{
		$this->LoadItem ( $id );
		if (array_key_exists ( $id, self::$list )) return self::$list [$id] ['act'];
		return false;
	}

	protected function LoadItem($id)
	{
		if (! array_key_exists ( $id, self::$list ))
		{
			$rs = self::Items ( "WHERE del=false AND id={$id}", '', 0, 1 );
			if ($rs && count ( $rs ) > 0) self::$list [$rs [0] ['id']] = $rs [0];
		}
	}

	public static function Count($where = '')
	{
		$rs = DB::db ()->Fetch ( "
				SELECT COUNT(id) AS count
				FROM " . self::TABLE . "
				$where
				", array () );
		if ($rs && count ( $rs ) > 0 && $rs ['count'] > 0) return $rs ['count'];
		return 0;
	}
	
	public static function CountHRFRM($where = '')
	{
		$rs = DB::db ()->Fetch ( "
				SELECT COUNT(id) AS count
				FROM " . self::TABLEHRFRM . "
				$where
				", array () );
		if ($rs && count ( $rs ) > 0 && $rs ['count'] > 0) return $rs ['count'];
		return 0;
	}

	public static function Items($where = '', $order = '', $start = 0, $size = 20)
	{
		$limit = DB::db ()->Limit ( $start, $size );
		$rs = DB::db ()->FetchAll ( "
				SELECT *
				FROM " . self::TABLE . "
				$where
				$order
				$limit
				" );
		if ($rs && count ( $rs ) > 0)
		{
			return $rs;
		}
		return array ();
	}
	
	public static function ItemsTo($id, $where = '', $order = '', $start = 0, $size = 20)
	{
		$limit = DB::db ()->Limit ( $start, $size );
		$rs = DB::db ()->FetchAll ( "
				SELECT *
				FROM " . self::TABLE . "
				WHERE bpid = $id
				$order
				$limit
				" );
		if ($rs && count ( $rs ) > 0)
		{
		return $rs;
		}
		return array ();
	}
	
	public static function Itemshr($where = '', $order = '', $start = 0, $size = 20)
	{
		$limit = DB::db ()->Limit ( $start, $size );
		$rs = DB::db ()->FetchAll ( "
				SELECT *
				FROM " . self::TABLEHR . "
				$where
				$order
				$limit
				" );
		if ($rs && count ( $rs ) > 0)
		{
			return $rs;
		}
		return array ();
	}
	
	public static function ItemsHRFRM($where = '', $order = '', $start = 0, $size = 20)
	{
		$limit = DB::db ()->Limit ( $start, $size );
		$rs = DB::db ()->FetchAll ( "
				SELECT *
				FROM " . self::TABLEHRFRM . "
				$where
				$order
				$limit
				" );
		if ($rs && count ( $rs ) > 0)
		{
		return $rs;
		}
		return array ();
	}
	
	public static function ItemsDoloadToHR($id)
	{
		$rs2 = DB::db ()->FetchAll ( "
				SELECT *
				FROM " . self::TABLEHR . "
				WHERE id = $id
				" );
		if ($rs2 && count ( $rs2 ) > 0)
		{
		return $rs2;
		}
		return array ();
	}
	
	public static function ItemsDoloadToHRFRM($id)
	{
		$rs2 = DB::db ()->FetchAll ( "
				SELECT *
				FROM " . self::TABLEHRFRM . "
				WHERE id = $id
				" );
		if ($rs2 && count ( $rs2 ) > 0)
		{
		return $rs2;
		}
		return array ();
	}
	
	public static function ItemsDoloadTo($id)
	{
		$rs2 = DB::db ()->FetchAll ( "
				SELECT *
				FROM " . self::TABLE . "
				WHERE id = $id
				" );
		if ($rs2 && count ( $rs2 ) > 0)
		{
			return $rs2;
		}
		return array ();
	}
	
	public static function AddHRFRM( $furl, $filename, $savename, $filesize, $downloads )
	{
		DB::db ()->Fetch ( "
				INSERT INTO " . self::TABLEHRFRM . "( furl, filename, savename, filesize, downloads)
					VALUES(?,?,?,?,?)
				", array ( $furl, $filename, $savename, $filesize, $downloads ) );
	}
	
	public static function AddHR( $furl, $filename, $savename, $filesize, $downloads )
	{
		DB::db ()->Fetch ( "
				INSERT INTO " . self::TABLEHR . "( furl, filename, savename, filesize, downloads)
					VALUES(?,?,?,?,?)
				", array ( $furl, $filename, $savename, $filesize, $downloads ) );
	}

	public static function Add($id,$furl,$filename,$savename,$filesize,$downloads)
	{	
		DB::db ()->Fetch ( "
				INSERT INTO " . self::TABLE . "( bpid, furl, filename, savename, filesize, downloads)
					VALUES(?,?,?,?,?,?)
					RETURNING id
				", array ( $id, $furl, $filename, $savename, $filesize, $downloads ) );
	}
	
	public static function Edit($id, $code, $name, $taxnumber, $place, $iscustomer, $isvendor, $memo)
	{
		DB::db ()->Query ( "
				UPDATE " . self::TABLE . "
				SET code=?, name=?, taxnumber=?, place=?, iscustomer=?, isvendor=?, memo=?
				WHERE id=?
				", array ( $code, $name, $taxnumber, $place, $iscustomer, $isvendor, $memo, $id ) );
	}

	public static function Delete($id)
	{
		DB::db ()->Query ( "
				UPDATE " . self::TABLE . "
				SET del=true
				WHERE id=?
				", array ( $id ) );
	}

	public static function Login($username, $password)
	{
		$rs = DB::db ()->Fetch ( "
				SELECT id, act
				FROM " . self::TABLE . "
				WHERE LOWER(username)=? AND password=? AND del=false
				LIMIT 1
				", array ( $username , md5 ( $password ) ) );
		if ($rs && count ( $rs ) > 0)
		{
			if ($rs ['act']) return $rs ['id'];
			return false;
		}
		return null;
	}
}
?>