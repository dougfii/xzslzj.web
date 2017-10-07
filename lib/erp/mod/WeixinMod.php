<?php
define ( "TOKEN", "greentowngtkj159357" );
class WeixinMod implements IMod
{

	public function index()
	{
		$this->ResponseMsg ();
	}

	protected function Head()
	{
		$view = View::Factory ( 'Head' );
		echo $view->Render ();
	}

	protected function Foot()
	{
		$view = View::Factory ( 'Foot' );
		echo $view->Render ();
	}
	
	// 注册接口检验
	public function Valid()
	{
		$wx = new WeixinAPICls ();
		$wx->valid ();
	}

	public function ResponseMsg()
	{
		$wx = new WeixinAPICls ();
		$wx->responseMsg ();
	}

	public function SendMsg()
	{
		$this->Head ();
		
		$view = View::Factory ( 'WeixinSendMsg' );
		echo $view->Render ();
		
		$this->Foot ();
	}
}
?>