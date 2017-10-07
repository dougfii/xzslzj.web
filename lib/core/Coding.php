<?php
class Coding
{
	// TODO：!!! 本类只有部分测试，正式使用还需要重测
	public static function Unicode2UTF8($str)
	{
		$ret = "";
		if ($str < 0x80)
		{
			$ret .= $str;
		}
		else if ($str < 0x800)
		{
			$ret .= chr ( 0xC0 | $str >> 6 );
			$ret .= chr ( 0x80 | $str & 0x3F );
		}
		else if ($str < 0x10000)
		{
			$ret .= chr ( 0xE0 | $str >> 12 );
			$ret .= chr ( 0x80 | $str >> 6 & 0x3F );
			$ret .= chr ( 0x80 | $str & 0x3F );
		}
		else if ($str < 0x200000)
		{
			$ret .= chr ( 0xF0 | $str >> 18 );
			$ret .= chr ( 0x80 | $str >> 12 & 0x3F );
			$ret .= chr ( 0x80 | $str >> 6 & 0x3F );
			$ret .= chr ( 0x80 | $str & 0x3F );
		}
		return $ret;
	}

	public static function uc2html($str)
	{
		$ret = '';
		
		for($i = 0; $i < strlen ( $str ) / 2; $i ++)
		{
			
			$charcode = ord ( $str [$i * 2] ) + 256 * ord ( $str [$i * 2 + 1] ); // 得到每个unicode
			
			if ($charcode < 127) 

			$ret .= chr ( $charcode ); // 127以前ASCII,UTF-8一样
			
			else 

			$ret .= self::Unicode2UTF8 ( $charcode ); // 转换成UTF-8
		}
		
		return $ret;
	}
	
	// 将内容进行UNICODE编码
	public static function UnicodeEncoder($str)
	{
		$str = iconv ( 'UTF-8', 'UCS-2', $str );
		$len = strlen ( $str );
		$val = '';
		for($i = 0; $i < $len - 1; $i = $i + 2)
		{
			$c = $str [$i];
			$c2 = $str [$i + 1];
			if (ord ( $c ) > 0)
			{ // 两个字节的文字
				$val .= '\u' . base_convert ( ord ( $c ), 10, 16 ) . base_convert ( ord ( $c2 ), 10, 16 );
			}
			else
			{
				$val .= $c2;
			}
		}
		return $val;
	}
	
	// 将UNICODE编码后的内容进行解码
	public static function UnicodeDecoder($str)
	{
		// 转换编码，将Unicode编码转换成可以浏览的utf-8编码
		$pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
		preg_match_all ( $pattern, $str, $matches );
		if (! empty ( $matches ))
		{
			print_r ( $matches );
			$str = '';
			for($j = 0; $j < count ( $matches [0] ); $j ++)
			{
				$val = $matches [0] [$j];
				if (strpos ( $val, '\\u' ) === 0)
				{
					$code = base_convert ( substr ( $val, 2, 2 ), 16, 10 );
					$code2 = base_convert ( substr ( $val, 4 ), 16, 10 );
					$c = chr ( $code ) . chr ( $code2 );
					$c = iconv ( 'UCS-2', 'UTF-8', $c );
					$str .= $c;
				}
				else
				{
					$str .= $val;
				}
			}
		}
		return $str;
	}

	public static function UTF82GBK($str)
	{
		return iconv ( "UTF-8", "GBK//IGNORE", $str );
	}

	public static function GBK2UTF8($str)
	{
		return mb_convert_encoding ( $str, "UTF-8", "GBK" );
	}

	public static function utfdecode($url) // unicode解码 (测试可行)
	{
		preg_match_all ( '/%u([[:alnum:]]{4})/', $url, $a );
		foreach ( $a [1] as $uniord )
		{
			$dec = hexdec ( $uniord );
			$utf = '';
			if ($dec < 128)
			{
				$utf = chr ( $dec );
			}
			else if ($dec < 2048)
			{
				$utf = chr ( 192 + (($dec - ($dec % 64)) / 64) );
				$utf .= chr ( 128 + ($dec % 64) );
			}
			else
			{
				$utf = chr ( 224 + (($dec - ($dec % 4096)) / 4096) );
				$utf .= chr ( 128 + ((($dec % 4096) - ($dec % 64)) / 64) );
				$utf .= chr ( 128 + ($dec % 64) );
			}
			$url = str_replace ( '%u' . $uniord, $utf, $url );
		}
		return urldecode ( $url );
	}

	public static function test($str)
	{
		return mb_convert_encoding ( $str, "UCS-2", "UTF-8" );
	}

	public static function JSON2UTF8($str)
	{
        //return preg_replace ( "#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str );
        return preg_replace_callback("#\\\u([0-9a-f]{4})#i", function ($matchs) {
            return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
        }, $str);
	}
}
?>