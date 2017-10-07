<?php
class Dispatcher
{
	private static $instance = null;
	
	public static function Instance()
	{
		if (self::$instance == null)
		{
			self::$instance = new self ();
		}
		return self::$instance;
	}
	
	public function Dispatch($mod = null, $act = null, $params = null)
	{
		if (is_null ( $mod )) $mod = Url::Req ( 'm', 'index', 'str' );
		if (is_null ( $act )) $act = Url::Req ( 'a', 'index', 'str' );
		if (is_null ( $params )) $params = array ();
		if (! ctype_alnum ( $mod )) throw new Exception ( "Invalid module name '$mod'" );
		

		$mod = ucwords ( $mod );
		$cls = $mod . 'Mod';
		
		if (! class_exists ( $cls, true )) throw new Exception ( "Module '$mod' not found" );
		
		$ctl = new $cls ();
		if (! is_callable ( array ($ctl, $act ) )) throw new Exception ( "Action '$act' not found in the controller '$ctl'" );
		
		try
		{
			return call_user_func_array ( array ($ctl, $act ), $params );
		}
		catch ( Exception $e )
		{
			throw new Exception ( "Module '$mod' call '$act' action has error" );
		}
	}
}
?>