<?php
class Format
{

	public static function DateString($val)
	{
		if (empty ( $val )) return $val;
		list ( $date , $time ) = preg_split ( '/\s/', $val );
		return $date;
	}

	public static function MonthDayString($val)
	{
		if (empty ( $val )) return $val;
		list ( $date , $time ) = preg_split ( '/\s/', $val );
		list ( $year , $month , $day ) = preg_split ( '/-/', $date );
		return "{$month}-{$day}";
	}

	public static function IntString($val)
	{
		return sprintf ( "%d", $val );
	}

	public static function PriceString($val, $percent = 4)
	{
		return sprintf ( "%.{$percent}f", $val );
	}

	public static function NumberString($val)
	{
		return sprintf ( "%.4f", $val );
	}

	public static function RateString($val)
	{
		return sprintf ( "%.2f", $val );
	}

	public static function BoolString($val, $yes = '√', $no = '')
	{
		return $val ? $yes : $no;
	}

	public static function SexString($val)
	{
		return $val ? '女' : '男';
	}

	public static function PercentString($rate)
	{
		return sprintf ( "%d%%", $rate * 100 );
	}

	public static function BytesString($size)
	{
		$bytes = array ( '' , 'K' , 'M' , 'G' , 'T' );
		foreach ( $bytes as $val )
		{
			if ($size > 1024) $size = $size / 1024;
			else break;
		}
		return round ( $size, 1 ) . " " . $val;
	}
}
?>