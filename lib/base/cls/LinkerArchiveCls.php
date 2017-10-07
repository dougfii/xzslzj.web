<?php
class LinkerArchiveCls
{
	const DOMAIN = 'system';
	protected static $archive = 'linker';
	protected static $elements = array ();
	private static $instance = null;

	public static function Instance()
	{
		if (self::$instance == null) self::$instance = new self ();
		return self::$instance;
	}

	private function __construct()
	{
		$this->Load ();
	}

	protected function Load()
	{
		$archive = new Archive ();
		self::$elements = $archive->get ( self::DOMAIN, self::$archive );
	}

	protected function Save()
	{
		$cache = new Archive ();
		$cache->Set ( self::$elements, self::DOMAIN, self::$archive );
	}

	public static function Clear()
	{
		$cache = new Archive ();
		$cache->Delete ( self::DOMAIN, self::$archive );
	}

	public function Get()
	{
		return self::$elements;
	}

	public function Set($elements)
	{
		self::$elements = $elements;
		$this->Save ();
	}
}
?>