<?php
class BaseStateCls
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
		//$this->load ();
	}

	public function GetName($id)
	{
		if (array_key_exists ( $id, self::$list ))
		{
			return self::$list [$id];
		}
		return false;
	}

	public function Items()
	{
		return self::$list;
	}

	/* protected function load()
	{
		require_once BASE_DAT_PATH . 'crm.php';
		self::$list = $GLOBALS ['BASE_STATE'];
	} */
}
?>