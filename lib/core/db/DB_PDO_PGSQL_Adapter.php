<?php
class DB_PDO_PGSQL_Adapter extends DB_PDO_MYSQL_Adapter
{

	public function __construct($conf, $driver = 'pgsql')
	{
		parent::__construct ( $conf, $driver );
	}

	public function Limit($start, $size)
	{
		if (! ($size === 0 && $start === 0)) return "LIMIT {$size} OFFSET {$start}";
		return '';
	}

	public function IsErrorNumber($e, $errno)
	{
		// map MySQL driver-specific error codes to PostgreSQL SQLSTATE
		$map = array ( 		// MySQL: Unknown database '%s'
		               // PostgreSQL: database "%s" does not exist
		'1049' => '08006' , 
				
				// MySQL: Table '%s' already exists
				// PostgreSQL: relation "%s" already exists
				'1050' => '42P07' , 
				
				// MySQL: Unknown column '%s' in '%s'
				// PostgreSQL: column "%s" does not exist
				'1054' => '42703' , 
				
				// MySQL: Duplicate column name '%s'
				// PostgreSQL: column "%s" of relation "%s" already exists
				'1060' => '42701' , 
				
				// MySQL: Duplicate entry '%s' for key '%s'
				// PostgreSQL: duplicate key violates unique constraint
				'1062' => '23505' , 
				
				// MySQL: Can't DROP '%s'; check that column/key exists
				// PostgreSQL: index "%s" does not exist
				'1091' => '42704' , 
				
				// MySQL: Table '%s.%s' doesn't exist
				// PostgreSQL: relation "%s" does not exist
				'1146' => '42P01' );
		
		if (preg_match ( '/([0-9]{2}[0-9P][0-9]{2})/', $e->getMessage (), $match ))
		{
			return $match [1] == $map [$errno];
		}
		return false;
	}
}
?>