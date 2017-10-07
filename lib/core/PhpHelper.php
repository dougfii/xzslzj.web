<?php
class PhpHelper
{

	public static function CheckSyntax($content)
	{
		$content = Url::Unsanitize ( $content );
		$content = "return true; " . $content;
		return @eval ( $content ) !== false;
	}
}
?>