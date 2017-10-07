<?php
class Timer
{
	private $timerStart;
	private $memoryStart;
	
	public function __construct()
	{
		$this->Init ();
	}
	
	public function Init()
	{
		$this->timerStart = $this->GetMicroTime ();
		$this->memoryStart = $this->GetMemoryUsage ();
	}
	
	public function GetTime($decimals = 6)
	{
		return number_format ( $this->GetMicroTime () - $this->timerStart, $decimals, '.', '' );
	}
	
	public function GetTimeMS($decimals = 6)
	{
		return number_format ( 1000 * ($this->GetMicroTime () - $this->timerStart), $decimals, '.', '' );
	}
	
	public function GetMemory()
	{
		return $this->GetMemoryUsage () - $this->memoryStart;
	}
	
	public function GetMemoryLeak()
	{
		return "Memory delta: " . Common::getPrettySizeFromBytes ( $this->GetMemory () );
	}
	
	public function __toString()
	{
		return "Time elapsed: " . $this->GetTime () . "s";
	}
	
	private function GetMicroTime()
	{
		list ( $micro_seconds, $seconds ) = explode ( " ", microtime () );
		return (( float ) $micro_seconds + ( float ) $seconds);
	}
	
	private function GetMemoryUsage()
	{
		if (function_exists ( 'memory_get_usage' ))
		{
			return memory_get_usage ();
		}
		return 0;
	}
}
?>