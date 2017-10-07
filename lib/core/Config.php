<?php
class Config
{
	private static $instance = null;
	
	protected $initialized = false;
	protected $global = false;
	protected $user = false;
	
	public $config = array ();
	
	static public function Instance()
	{
		if (self::$instance == null)
		{
			self::$instance = new self ();
		}
		return self::$instance;
	}
	
	public function Init($u = null, $g = null)
	{
		if (is_null ( $g ))
		{
			$g = BASE_SYS_PATH . '/conf/global.ini.php';
		}
		$this->global = parse_ini_file ( $g, true );
		
		if (is_null ( $u ))
		{
			$u = BASE_SYS_PATH . '/conf/config.ini.php';
		}
		$this->user = parse_ini_file ( $u, true );
		if ($this->user)
		{
			foreach ( $this->user as $section => &$sectionValues )
			{
				foreach ( $sectionValues as $name => &$value )
				{
					if (is_array ( $value ))
					{
						$value = array_map ( "html_entity_decode", $value );
					}
					else
					{
						$value = html_entity_decode ( $value );
					}
				}
			}
		}
		$this->initialized = true;
	}
	
	public function __get($name)
	{
		if (! $this->initialized)
		{
			$this->Init ();
		}
		
		$section = array ();
		if (isset ( $this->global [$name] ))
		{
			$section = array_merge ( $section, $this->global [$name] );
		}
		if (isset ( $this->user [$name] ))
		{
			$section = array_merge ( $section, $this->user [$name] );
		}
		if (isset ( $this->config [$name] ))
		{
			$section = array_merge ( $section, $this->config [$name] );
		}
		return count ( $section ) ? $section : null;
	}
}
?>