<?php
class Inet
{

	public static function GetIP($ip = false)
	{
		if ($ip === false) $ip = self::GetIPString ();
		
		// accept ipv4-mapped addresses
		if (! strncmp ( $ip, '::ffff:', 7 ))
		{
			$ip = substr ( $ip, 7 );
		}
		// Ip is A.B.C.D:54287
		if (($posColon = strpos ( $ip, ':' )) != false && $posColon > 6)
		{
			$ip = substr ( $ip, 0, $posColon );
		}
		return sprintf ( "%u", ip2long ( $ip ) );
	}

	public static function GetIPString()
	{
		static $c = null;
		
		if (is_null ( $c ))
		{
			$c = @Config::Instance ()->General ['proxy_client_headers'];
			
			if (! is_array ( $c ))
			{
				$c = array ();
			}
		}
		
		$default = '0.0.0.0';
		if (isset ( $_SERVER ['REMOTE_ADDR'] ))
		{
			$default = $_SERVER ['REMOTE_ADDR'];
		}
		
		return self::GetProxyFromHeader ( $default, $c );
	}

	public static function GetIPAddress($long_ip)
	{
		return long2ip ( $long_ip );
	}

	public static function GetProxyFromHeader($def, $proxyHeaders)
	{
		foreach ( $proxyHeaders as $proxyHeader )
		{
			if (! empty ( $_SERVER [$proxyHeader] ))
			{
				$p = Common::GetLastFromList ( $_SERVER [$proxyHeader], $def );
				if (strlen ( $p ) && stripos ( $p, 'unknown' ) === false)
				{
					return $p;
				}
			}
		}
		return $def;
	}
}
?>