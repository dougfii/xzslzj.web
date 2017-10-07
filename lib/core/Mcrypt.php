<?php
class Mcrypt
{

	public static function DES_Encode($key, $str, $mode = MCRYPT_MODE_ECB)
	{
		return self::Encode ( MCRYPT_DES, $key, $str, $mode );
	}

	public static function DES_Decode($key, $str, $mode = MCRYPT_MODE_ECB)
	{
		return self::Decode ( MCRYPT_DES, $key, $str, $mode );
	}

	private static function Encode($cipher, $key, $str, $mode)
	{
		return mcrypt_encrypt ( $cipher, $key, $str, $mode, self::CreateIV ( $cipher, $mode ) );
	}

	private static function Decode($cipher, $key, $str, $mode)
	{
		return mcrypt_decrypt ( $cipher, $key, $str, $mode, self::CreateIV ( $cipher, $mode ) );
	}

	private static function CreateIV($cipher, $mode)
	{
		return mcrypt_create_iv ( mcrypt_get_iv_size ( $cipher, $mode ), MCRYPT_RAND );
	}
}
?>