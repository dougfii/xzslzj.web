<?php
abstract class DB_Adapter
{
	protected static $log_event = false;

	abstract public function Connect();

	abstract public function Disconnect();

	abstract public function FetchAll($query, $parameters = array());

	abstract public function Fetch($query, $parameters = array());

	public function FetchRow($query, $parameters = array())
	{
		return $this->fetch ( $query, $parameters );
	}

	public function FetchOne($query, $parameters = array())
	{
		$result = $this->fetch ( $query, $parameters );
		return is_array ( $result ) && ! empty ( $result ) ? reset ( $result ) : false;
	}

	public function Exec($query, $parameters = array())
	{
		return $this->fetch ( $query, $parameters );
	}

	abstract public function Query($query, $parameters = array());

	abstract public function BeginTransaction();

	abstract public function Commit();

	abstract public function RollBack();

	abstract public function RowCount($queryResult);

	abstract public function LastInsertID();

	abstract public function IsErrorNumber($e, $errno);

	abstract public function Limit($start, $size);

	public function LogEvent($query, $parameters = array(), $timer = null)
	{
		self::$log_event = false;
		
		if (is_null ( $this->connection ))
		{
			return;
		}
		
		$query = trim ( $query );
		if (stripos ( $query, 'SELECT' ) !== 0)
		{
			$spent = $memory = 0;
			if (! is_null ( $timer ))
			{
				$spent = $timer->GetTimeMS ();
				$memory = $timer->GetMemory ();
			}
			
			if (! empty ( $parameters ))
			{
				$query .= "\n\nParameters:\n" . var_export ( $parameters, true );
			}
			
			$this->query ( "INSERT INTO t_log_event(event, spent, memory) VALUES(?,?,?)", array ( $query , $spent , $memory ) );
		}
		
		self::$log_event = true;
	}
}
class DB_Adapter_Exception extends Exception
{
}
?>