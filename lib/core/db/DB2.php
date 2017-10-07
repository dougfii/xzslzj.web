<?php
class DB2
{
	protected static $db = null;

	public function __construct()
	{
		self::Connect ();
	}

	public function __destruct()
	{
		self::Disconnect ();
	}

	public static function Factory($conf)
	{
		switch ($conf ['adapter'])
		{
			case 'PDO_MYSQL' :
				return new DB_PDO_MYSQL_Adapter ( $conf );
			
			case 'PDO_PGSQL' :
				return new DB_PDO_PGSQL_Adapter ( $conf );
			
			case 'PDO_OCI' :
				return new DB_PDO_OCI_Adapter ( $conf );
		}
		
		throw new Exception ( 'Unsupported database adapter ' . $conf ['adapter'] );
	}

	public static function Connect()
	{
		if (! is_null ( self::$db ))
		{
			return;
		}
		
		$db = null;
		$conf = Config::Instance ()->database2;
		$db = self::Factory ( $conf );
		$db->connect ();
		
		self::$db = $db;
	}

	public static function Disconnect()
	{
		if (isset ( self::$db ))
		{
			self::$db->disconnect ();
			self::$db = null;
		}
	}

	public static function db()
	{
		if (is_null ( self::$db )) self::Connect ();
		return self::$db;
	}

	public static function SqlIn($rs)
	{
		return "'" . implode ( "','", $rs ) . "'";
	}
}
?>