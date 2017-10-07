<?php
class View implements IView
{
	private $template = false;

	public function __construct($file)
	{
		$this->template = new Template ( $file );
	}

	public function __set($key, $val)
	{
		$this->template->Set ( $key, $val );
	}

	public function Set($key, $val)
	{
		$this->template->Set ( $key, $val );
	}

	public function Render()
	{
		return $this->template->Fetch ();
	}

	public static function Factory($file)
	{
		$file = BASE_TPL_PATH . $file . '.tpl';
		return new View ( $file );
	}
	
	/*
	 * public function sub($file = '', $map = array()) { $t = new Template ( $file ); foreach ( $map as $k => $v ) { $t->set ( $k, $v ); } return $t->fetch (); }
	 */
}
?>