<?php
class Sys
{

	public static function IsWindows()
	{
		return DIRECTORY_SEPARATOR == '\\';
	}
}
?>