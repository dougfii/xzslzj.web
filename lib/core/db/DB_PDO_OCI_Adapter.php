<?php
class DB_PDO_OCI_Adapter extends DB_Adapter
{

	public function __construct($conf, $driver = 'oci')
	{
		$this->dsn = "$driver:dbname=//{$conf['host']}:{$conf['port']}/{$conf['dbname']};";
		$this->dsn .= isset ( $conf ['charset'] ) ? "charset={$conf ['charset']};" : '';
		$this->username = $conf ['username'];
		$this->password = $conf ['password'];
		// $this->charset = isset ( $conf ['charset'] ) ? $conf ['charset'] : null;
		
		if (isset ( $conf ['log_db_event'] )) self::$log_event = $conf ['log_db_event'];
	}

	public function __destruct()
	{
		$this->disconnect ();
	}

	public function Connect()
	{
		$this->connection = @new PDO ( $this->dsn, $this->username, $this->password, $config = array () );
		$this->connection->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$this->password = '';
		
		// if (! empty ( $this->charset ))
		// {
		// $sql = "SET NAMES '" . $this->charset . "'";
		// $this->connection->exec ( $sql );
		// }
		
		if (self::$log_event)
		{
			$this->LogEvent ( 'connect' );
		}
	}

	public function Disconnect()
	{
		$this->connection = null;
	}

	public function FetchAll($query, $parameters = array())
	{
		try
		{
			$sth = $this->query ( $query, $parameters );
			if ($sth === false)
			{
				return false;
			}
			return $sth->FetchAll ( PDO::FETCH_ASSOC );
		}
		catch ( PDOException $e )
		{
			throw new DB_Adapter_Exception ( "Error query: " . $e->getMessage () );
		}
	}

	public function Fetch($query, $parameters = array())
	{
		try
		{
			$sth = $this->query ( $query, $parameters );
			if ($sth === false)
			{
				return false;
			}
			return $sth->fetch ( PDO::FETCH_ASSOC );
		}
		catch ( PDOException $e )
		{
			throw new DB_Adapter_Exception ( "Error query: " . $e->getMessage () );
		}
	}

	public function Query($query, $parameters = array())
	{
		if (is_null ( $this->connection ))
		{
			return false;
		}
		
		try
		{
			if (self::$log_event)
			{
				$timer = new Timer ();
			}
			
			if (! is_array ( $parameters ))
			{
				$parameters = array ( $parameters );
			}
			$sth = $this->connection->prepare ( $query );
			$sth->execute ( $parameters );
			
			if (self::$log_event)
			{
				$this->LogEvent ( $query, $parameters, $timer );
			}
			
			return $sth;
		}
		catch ( PDOException $e )
		{
			throw new DB_Adapter_Exception ( "Error query: " . $e->getMessage () . " In query: $query Parameters: " . var_export ( $parameters, true ) );
		}
	}

	public function BeginTransaction()
	{
		return $this->connection->BeginTransaction ();
	}

	public function Commit()
	{
		return $this->connection->Commit ();
	}

	public function RollBack()
	{
		return $this->connection->RollBack ();
	}

	public function LastInsertID()
	{
		return $this->connection->LastInsertID ();
	}

	public function Limit($start, $size)
	{
		if (! ($size === 0 && $start === 0)) return "LIMIT {$size} OFFSET {$start}";
		return '';
	}

	public function IsErrorNumber($e, $errno)
	{
		if (preg_match ( '/([0-9]{4})/', $e->getMessage (), $match ))
		{
			return $match [1] == $errno;
		}
		return false;
	}

	public function RowCount($queryResult)
	{
		return $queryResult->RowCount ();
	}
}
?>