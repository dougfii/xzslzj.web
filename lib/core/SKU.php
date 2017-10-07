<?php
class SKU
{
	/*
	 * SKU rule 例：GTA5T0B 第一、二位 固定为GT开头 第三为 A-Z 第四至七位为 0-9、A-Z 去除引导字符GT, 支持 0-43670016计数
	 */
	const FLAG = 'GT'; // 引导字符
	const LENGTH = 7; // 编码全长
	const BASE = 1679616; // 因子 (后四位的乘积 36*36*36*36)
	                      
	//
	public static function Decode($serial)
	{
		if (strlen ( $serial ) == 7 && strpos ( $serial, self::FLAG ) === 0)
		{
			$s1 = substr ( $serial, strlen ( self::FLAG ), 1 );
			$s2 = substr ( $serial, strlen ( self::FLAG ) + 1, self::LENGTH - strlen ( self::FLAG ) + 1 );
			
			return (self::Alpha2Num ( $s1 ) * self::BASE) + base_convert ( $s2, 36, 10 );
		}
		return false;
	}

	public static function Encode($number)
	{
		$n1 = intval ( $number / self::BASE );
		$n2 = base_convert ( $number % self::BASE, 10, 36 );
		
		return self::FLAG . self::Num2Alpha ( $n1 ) . strtoupper ( str_pad ( $n2, self::LENGTH - strlen ( self::FLAG ) - 1, '0', STR_PAD_LEFT ) );
	}

	public static function Next($serial)
	{
		$num = self::Decode ( $serial );
		if ($num === false) $num = 0;
		return self::Encode ( $num + 1 );
	}

	public static function Num2Alpha($n)
	{
		$r = '';
		for($i = 1; $n >= 0 && $i < 10; $i ++)
		{
			$r = chr ( 0x41 + ($n % pow ( 26, $i ) / pow ( 26, $i - 1 )) ) . $r;
			$n -= pow ( 26, $i );
		}
		return $r;
	}

	public static function Alpha2Num($a)
	{
		$r = 0;
		$l = strlen ( $a );
		for($i = 0; $i < $l; $i ++)
		{
			$r += pow ( 26, $i ) * (ord ( $a [$l - $i - 1] ) - 0x40);
		}
		return $r - 1;
	}
	// num2alpha2和alpha2num2暂时不用
	function num2alpha2($n)
	{
		for($r = ""; $n >= 0; $n = intval ( $n / 26 ) - 1)
			$r = chr ( $n % 26 + 0x41 ) . $r;
		return $r;
	}

	function alpha2num2($a)
	{
		$l = strlen ( $a );
		$n = 0;
		for($i = 0; $i < $l; $i ++)
			$n = $n * 26 + ord ( $a [$i] ) - 0x40;
		return $n - 1;
	}
}
?>