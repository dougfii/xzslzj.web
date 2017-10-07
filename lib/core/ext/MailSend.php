<?php
class MailSend extends PHPMailer
{

	public function __construct()
	{
		$this->From = Config::Instance ()->smtp ['user'];
		$this->FromName = Config::Instance ()->smtp ['name'];
		$this->Password = Config::Instance ()->smtp ['pass'];
		$this->Username = Config::Instance ()->smtp ['user'];
		$this->Host = Config::Instance ()->smtp ['host'];
		$this->Mailer = 'smtp';
		$this->WordWrap = 75;
		$this->CharSet = 'UTF-8';
		$this->SMTPAuth = true;
	}

	public function SendToSingle($to, $subject, $body)
	{
		$this->IsHTML ( true );
		$this->Subject = $subject;
		$this->Body = $body;
		$this->addAddress ( $to );
		
		return $this->send ();
	}
}
?>