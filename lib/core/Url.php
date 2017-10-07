<?php
class Url
{

	public static function Req($name, $def = null, $type = null, $req = null)
	{
		if (is_null ( $req ))
		{
			$req = $_GET + $_POST;
		}
		
		$def = self::Sanitizes ( $def );
		
		if ($type === 'int')
		{
			$type = 'integer';
		}
		elseif ($type === 'str')
		{
			$type = 'string';
		}
		
		if (empty ( $name ) || ! isset ( $req [$name] ) || (! is_array ( $req [$name] ) && strlen ( $req [$name] ) === 0))
		{
			if (is_null ( $def ))
			{
				throw new Exception ( "The parameter '$name' isn't set in the Request, and a default value wasn't provided." );
			}
			else
			{
				if (! is_null ( $type ) && in_array ( $type, array ( 'string' , 'integer' , 'array' ) ))
				{
					settype ( $def, $type );
				}
				return $def;
			}
		}
		
		$v = self::Sanitizes ( $req [$name] );
		
		if (! is_null ( $type ))
		{
			$ok = false;
			
			if ($type === 'string')
			{
				if (is_string ( $v )) $ok = true;
			}
			elseif ($type === 'integer')
			{
				if ($v == ( string ) ( int ) $v) $ok = true;
			}
			elseif ($type === 'float')
			{
				if ($v == ( string ) ( float ) $v) $ok = true;
			}
			elseif ($type === 'array')
			{
				if (is_array ( $v )) $ok = true;
			}
			else
			{
				throw new Exception ( "\$varType specified is not known. It should be one of the following: array, int, integer, float, string" );
			}
			
			if ($ok === false)
			{
				if ($def === null)
				{
					throw new Exception ( "The parameter '$name' doesn't have a correct type, and a default value wasn't provided." );
				}
				else
				{
					settype ( $def, $type );
					return $def;
				}
			}
			settype ( $v, $type );
		}
		return $v;
	}

	public static function Sanitizes($val)
	{
		if (is_numeric ( $val ))
		{
			return $val;
		}
		elseif (is_string ( $val ))
		{
			$val = self::Sanitize ( $val );
			
			// Undo the damage caused by magic_quotes; deprecated in php 5.3 but not removed until php 6
			if (version_compare ( PHP_VERSION, '6' ) === - 1 && get_magic_quotes_gpc ())
			{
				$val = stripslashes ( $val );
			}
		}
		elseif (is_array ( $val ))
		{
			foreach ( array_keys ( $val ) as $key )
			{
				$newKey = $key;
				$newKey = self::Sanitizes ( $newKey );
				if ($key != $newKey)
				{
					$val [$newKey] = $val [$key];
					unset ( $val [$key] );
				}
				
				$val [$newKey] = self::Sanitizes ( $val [$newKey] );
			}
		}
		elseif (! is_null ( $val ) && ! is_bool ( $val ))
		{
			throw new Exception ( "The value to escape has not a supported type. Value = " . var_export ( $val, true ) );
		}
		return $val;
	}

	public static function Sanitize($val)
	{
		$val = html_entity_decode ( $val, ENT_QUOTES, 'UTF-8' );
		$val = str_replace ( array ( '\n' , '\r' , '\0' ), '', $val );
		return htmlspecialchars ( $val, ENT_QUOTES, 'UTF-8' );
	}

	public static function Unsanitize($val)
	{
		return htmlspecialchars_decode ( $val, ENT_QUOTES );
	}

	public static function Output($val)
	{
		$val = str_replace ( array ( ' ' , '　' ), '&nbsp;', $val );
		$val = nl2br ( $val );
		return $val;
	}

	public static function URL_FULL()
	{
		return self::SCHEME () . '://' . self::HOST () . self::SCRIPT_NAME () . self::QUERY_STRING ();
	}

	public static function URL_WITHOUT_QUERYSTRING()
	{
		return self::SCHEME () . '://' . self::HOST () . self::SCRIPT_NAME ();
	}

	public static function URL_WITHOUT_FILENAME()
	{
		return self::SCHEME () . '://' . self::HOST () . self::SCRIPT_PATH ();
	}

	public static function SCHEME()
	{
		if (isset ( $_SERVER ['HTTPS'] ) && ($_SERVER ['HTTPS'] == 'on' || $_SERVER ['HTTPS'] === true))
		{
			return 'https';
		}
		return 'http';
	}

	public static function HOST()
	{
		if (isset ( $_SERVER ['HTTP_HOST'] ))
		{
			return self::Sanitize ( $_SERVER ['HTTP_HOST'] );
		}
		
		return '';
	}

	public static function REQUEST_URI()
	{
		if (! empty ( $_SERVER ['REQUEST_URI'] )) return $_SERVER ['REQUEST_URI'];
		return '';
	}

	public static function SCRIPT_PATH()
	{
		$qs = self::SCRIPT_NAME ();
		$urlDir = dirname ( $qs . 'x' );
		$urlDir = str_replace ( '\\', '/', $urlDir );
		
		if (strlen ( $urlDir ) > 1)
		{
			$urlDir .= '/';
		}
		
		return $urlDir;
	}

	public static function SCRIPT_NAME()
	{
		$url = '';
		if (! empty ( $_SERVER ['PATH_INFO'] ))
		{
			$url = $_SERVER ['PATH_INFO'];
		}
		elseif (! empty ( $_SERVER ['REQUEST_URI'] ))
		{
			$uri = $_SERVER ['REQUEST_URI'];
			
			if (preg_match ( '~^https?://[^/]+($|/.*)~', $uri, $m ))
			{
				$uri = $m [1];
			}
			
			if (($pos = strpos ( $uri, "?" )) !== false)
			{
				$url = substr ( $uri, 0, $pos );
			}
			else
			{
				$url = $uri;
			}
		}
		
		if (empty ( $url ))
		{
			$url = $_SERVER ['SCRIPT_NAME'];
		}
		
		if (! isset ( $url [0] ) || $url [0] !== '/')
		{
			$url = '/' . $url;
		}
		return $url;
	}

	public static function QUERY_STRING()
	{
		$url = '';
		if (isset ( $_SERVER ['QUERY_STRING'] ) && ! empty ( $_SERVER ['QUERY_STRING'] ))
		{
			$url .= "?" . $_SERVER ['QUERY_STRING'];
		}
		return $url;
	}

	public static function REFERER()
	{
		if (! empty ( $_SERVER ['HTTP_REFERER'] ))
		{
			return $_SERVER ['HTTP_REFERER'];
		}
		return false;
	}

	public static function QUERY_STRING_ARRAY()
	{
		$qs = self::QUERY_STRING ();
		return self::GetQueryStringArray ( $qs );
	}

	public static function QUERY_STRING_MODIFY($params = array())
	{
		$a = self::QUERY_STRING_ARRAY ();
		
		foreach ( $params as $k => $v )
		{
			$a [$k] = $v;
		}
		
		$qs = self::SetQueryString ( $a );
		
		if (strlen ( $qs ) > 0)
		{
			return '?' . $qs;
		}
		
		return '';
	}

	public static function QUERY_STRING_DELETE($params = array())
	{
		$a = self::QUERY_STRING_ARRAY ();
		
		foreach ( $params as $k => $v )
		{
			if (array_key_exists ( $v, $a )) unset ( $a [$v] );
		}
		
		$qs = self::SetQueryString ( $a );
		
		if (strlen ( $qs ) > 0)
		{
			return '?' . $qs;
		}
		
		return '';
	}

	public static function GetQueryStringParam($qs, $param)
	{
		$v = self::GetQueryStringArray ( $qs );
		
		if (isset ( $v [$param] ))
		{
			return $v [$param];
		}
		
		return false;
	}

	public static function GetQueryStringArray($qs)
	{
		if (strlen ( $qs ) == 0)
		{
			return array ();
		}
		
		if ($qs [0] == '?')
		{
			$qs = substr ( $qs, 1 );
		}
		
		$f = '&';
		
		$qs = $f . $qs;
		
		$ref = trim ( $qs );
		
		$lst = explode ( $f, $ref );
		
		$nm = array ();
		
		foreach ( $lst as $v )
		{
			if (false !== strpos ( $v, '=' ))
			{
				$exploded = explode ( '=', $v );
				$name = $exploded [0];
				$v = $exploded [1];
			}
			else
			{
				$name = $v;
				$v = '';
			}
			
			$count = 0;
            //$tmp = preg_replace ( '/(\[|%5b)(]|%5d)$/i', '', $name, - 1, $count );
            $tmp = preg_replace_callback('/(\[|%5b)(]|%5d)$/i', function () {
                return '';
            }, $name, -1, $count);
			
			if (! empty ( $tmp ) && $count)
			{
				$name = $tmp;
				
				if (isset ( $nm [$name] ) == false || is_array ( $nm [$name] ) == false)
				{
					$nm [$name] = array ();
				}
				
				array_push ( $nm [$name], $v );
			}
			else if (! empty ( $name ))
			{
				$nm [$name] = $v;
			}
		}
		return $nm;
	}

	public static function SetQueryString($params)
	{
		$qs = '';
		foreach ( $params as $k => $v )
		{
			if (is_null ( $v ) || $v === false)
			{
				continue;
			}
			if (is_array ( $v ))
			{
				foreach ( $v as $a )
				{
					$qs .= $k . "[]=" . $a . "&";
				}
			}
			else
			{
				$qs .= $k . "=" . $v . "&";
			}
		}
		$qs = substr ( $qs, 0, - 1 );
		return $qs;
	}

	public static function RedirectReferer()
	{
		$referer = self::REFERER ();
		if ($referer !== false)
		{
			self::RedirectUrl ( $referer );
		}
		self::RedirectUrl ( self::URL_WITHOUT_QUERYSTRING () );
	}

	public static function RedirectUrl($url)
	{
		@header ( "Location: $url" );
		exit ();
	}

	public static function IsLocalUrl($url)
	{
		$pathContains = Sys::IsWindows () ? 'stripos' : 'strpos';
		
		if (! strncasecmp ( $url, 'http://', 7 ) || ! strncasecmp ( $url, 'https://', 8 ))
		{
			$offset = strpos ( $url, '://' );
			$current = strstr ( self::URL_WITHOUT_FILENAME (), '://' );
			if ($pathContains ( $url, $current, $offset ) === $offset)
			{
				return true;
			}
		}
		
		return false;
	}

	public static function GetLocalReferer()
	{
		$referer = self::REFERER ();
		
		if ($referer !== false && self::IsLocalUrl ( $referer ))
		{
			return $referer;
		}
		
		return false;
	}
}
?>