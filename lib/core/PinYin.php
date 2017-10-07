<?php
class PinYin
{
	private static $letters = array ( 'A' => 0xB0C4 , 'B' => 0xB2C0 , 'C' => 0xB4ED , 'D' => 0xB6E9 , 'E' => 0xB7A1 , 'F' => 0xB8C0 , 'G' => 0xB9FD , 'H' => 0xBBF6 , 'J' => 0xBFA5 , 'K' => 0xC0AB , 'L' => 0xC2E7 , 'M' => 0xC4C2 , 'N' => 0xC5B5 , 'O' => 0xC5BD , 'P' => 0xC6D9 , 'Q' => 0xC8BA , 'R' => 0xC8F5 , 'S' => 0xCBF9 , 'T' => 0xCDD9 , 'W' => 0xCEF3 , 'X' => 0xD188 , 'Y' => 0xD4D0 , 'Z' => 0xD7F9 );

	public static function GetFirstLetter($input)
	{
		$input = iconv ( 'UTF-8', 'GB18030', $input );
		$str = substr ( $input, 0, 1 );
		if ($str >= chr ( 0x81 ) && $str <= chr ( 0xfe ))
		{
			$num = hexdec ( bin2hex ( substr ( $input, 0, 2 ) ) );
			foreach ( self::$letters as $k => $v )
			{
				if ($v >= $num) break;
			}
			return $k;
		}
		else
		{
			return $str;
		}
	}
}
?>