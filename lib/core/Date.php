<?php
class Date
{
	public static $CHINESE_MONTY = array ( 1 => '一月' , 2 => '二月' , 3 => '三月' , 4 => '四月' , 5 => '五月' , 6 => '六月' , 7 => '七月' , 8 => '八月' , 9 => '九月' , 10 => '十月' , 11 => '十一月' , 12 => '十二月' );
	public static $CHINESE_WEEKDAY = array ( 1 => '一' , 2 => '二' , 3 => '三' , 4 => '四' , 5 => '五' , 6 => '六' , 7 => '日' );
	public static $CHINESE_QUARTERLY = array ( 1 => '一季度' , 2 => '二季度' , 3 => '三季度' , 4 => '四季度' );
	
	//
	public static function ToTime($str, $def = null)
	{
		$time = strtotime ( $str );
		if ($time === false) return is_null ( $def ) ? time () : $def;
		return $time;
	}

	public static function IsDatetime($str)
	{
		$time = strtotime ( $str );
		return ($time === false) ? false : true;
	}

	public static function AddSecond($str, $n = 1, $type = 's')
	{
		$time = self::ToTime ( $str, false );
		if ($time === false) return false;
		return self::DateString ( $time + $n, $type );
	}

	public static function AddMinute($str, $n = 1, $type = 's')
	{
		$time = self::ToTime ( $str, false );
		if ($time === false) return false;
		return self::DateString ( $time + $n * 60, $type );
	}

	public static function AddHour($str, $n = 1, $type = 's')
	{
		$time = self::ToTime ( $str, false );
		if ($time === false) return false;
		return self::DateString ( $time + $n * 3600, $type );
	}

	public static function AddDay($str, $n = 1, $type = 'd')
	{
		$time = self::ToTime ( $str, false );
		if ($time === false) return false;
		return self::DateString ( $time + $n * 86400, $type );
	}

	public static function AddMonth($str, $n = 1, $type = 'd')
	{
		$time = self::ToTime ( $str, false );
		if ($time === false) return false;
		$dy = self::Year ( $time );
		$dm = self::Month ( $time );
		$dd = self::Day ( $time );
		$dh = self::Hour ( $time );
		$di = self::Minute ( $time );
		$ds = self::Second ( $time );
		$y = floor ( ($dm + $n) / 12 );
		$m = ($dm + $n) % 12;
		$dx = mktime ( $dh, $di, $ds, $m + 1, 1, $dy + $y ) - 86400;
		if ($dm != self::Month ( $time + 86400 )) return self::DateString ( $dx, $type );
		if ($dy >= self::Day ( $dx )) return self::DateString ( $dx, $type );
		
		$dy = self::Year ( $dx );
		$dm = self::Month ( $dx );
		$dh = self::Hour ( $dx );
		$di = self::Minute ( $dx );
		$ds = self::Second ( $dx );
		return self::DateString ( mktime ( $dh, $di, $ds, $dm, $dd, $dy ), $type );
	}

	public static function AddYear($str, $n = 1, $type = 'd')
	{
		$time = self::ToTime ( $str, false );
		if ($time === false) return false;
		$dy = self::Year ( $time );
		$dm = self::Month ( $time );
		$dd = self::Day ( $time );
		$dh = self::Hour ( $time );
		$di = self::Minute ( $time );
		$ds = self::Second ( $time );
		$time = self::ToTime ( $dy + $n . '-01-01' );
		if (! self::IsLeapYear ( $time ) && $dm == 2 && $dd == 29) $dd = 28;
		return self::DateString ( mktime ( $dh, $di, $ds, $dm, $dd, $dy + $n ), $type );
	}

	public static function DiffDay($datetime1, $datetime2)
	{
		if (self::IsDatetime ( $datetime1 ) && self::IsDatetime ( $datetime2 )) return floor ( (self::ToTime ( $datetime1 ) - self::ToTime ( $datetime2 )) / 86400 );
		return false;
	}

	public static function DiffMonth($datetime1, $datetime2)
	{
		if (self::IsDatetime ( $datetime1 ) && self::IsDatetime ( $datetime2 ))
		{
			$time1 = self::ToTime ( $datetime1 );
			$time2 = self::ToTime ( $datetime2 );
			$d1y = self::Year ( $time1 );
			$d2y = self::Year ( $time2 );
			$d1m = self::Month ( $time1 );
			$d2m = self::Month ( $time2 );
			if ($d1y == $d2y) return floor ( $d1m - $d2m );
			else return ($d1y - $d2y) * 12 + ($d1m - $d2m);
		}
		return false;
	}

	public static function DiffYear($datetime1, $datetime2)
	{
		if (self::IsDatetime ( $datetime1 ) && self::IsDatetime ( $datetime2 )) return self::Year ( self::ToTime ( $datetime1 ) ) - self::Year ( self::ToTime ( $datetime2 ) );
		return false;
	}

	public static function Compare($datetime1, $datetime2, $type = 'd')
	{
		switch ($type)
		{
			case 'y' :
				return self::CompareDatetime ( $datetime1, $datetime2, 'Y' );
			case 'm' :
				return self::CompareDatetime ( $datetime1, $datetime2, 'Y-m' );
			case 'h' :
				return self::CompareDatetime ( $datetime1, $datetime2, 'Y-m-d H' );
			case 'i' :
				return self::CompareDatetime ( $datetime1, $datetime2, 'Y-m-d H:i' );
			case 's' :
				return self::CompareDatetime ( $datetime1, $datetime2, 'Y-m-d H:i:s' );
			default :
				return self::CompareDatetime ( $datetime1, $datetime2, 'Y-m-d' );
		}
	}

	public static function CompareDatetime($datetime1, $datetime2, $format = 'Y-m-d')
	{
		try
		{
			if (date ( $format, strtotime ( $datetime1 ) ) == date ( $format, strtotime ( $datetime2 ) ))
			{
				return 0;
			}
			elseif (date ( $format, strtotime ( $datetime1 ) ) > date ( $format, strtotime ( $datetime2 ) ))
			{
				return 1;
			}
			else
			{
				return - 1;
			}
		}
		catch ( Exception $e )
		{
			return null;
		}
	}
	
	// 重新格式化日期时间
	public static function Format($str, $type = 'd', $def = true)
	{
		switch ($type)
		{
			case 'y' :
				return self::FormatString ( $str, 'Y', $def );
			case 'm' :
				return self::FormatString ( $str, 'Y-m', $def );
			case 'h' :
				return self::FormatString ( $str, 'Y-m-d H', $def );
			case 'i' :
				return self::FormatString ( $str, 'Y-m-d H:i', $def );
			case 's' :
				return self::FormatString ( $str, 'Y-m-d H:i:s', $def );
			default :
				return self::FormatString ( $str, 'Y-m-d', $def );
		}
	}

	public static function FormatString($str, $format = 'Y-m-d H:i:s', $def = true)
	{
		$time = self::ToTime ( $str, $def );
		if ($time) return self::Datetime ( $time, $format );
		return false;
	}
	
	// 日期时间
	public static function Datetime($time = null, $format = 'Y-m-d H:i:s')
	{
		return date ( $format, is_null ( $time ) ? time () : $time );
	}
	
	// 日期
	public static function DateString($time = null, $type = 'd')
	{
		switch ($type)
		{
			case 'y' :
				return self::Datetime ( $time, 'Y' );
			case 'm' :
				return self::Datetime ( $time, 'Y-m' );
			case 'h' :
				return self::Datetime ( $time, 'Y-m-d H' );
			case 'i' :
				return self::Datetime ( $time, 'Y-m-d H:i' );
			case 's' :
				return self::Datetime ( $time, 'Y-m-d H:i:s' );
			default :
				return self::Datetime ( $time, 'Y-m-d' );
		}
	}
	
	// 一个月的天数
	public static function MonthDays($time = null)
	{
		return self::Datetime ( $time, 't' );
	}
	
	// 一个星期的周几
	public static function DayOfWeek($time = null)
	{
		return self::Datetime ( $time, 'N' );
	}
	
	// 一年的第几周
	public static function WeekOfYear($time = null)
	{
		return self::Datetime ( $time, 'W' );
	}
	
	// 闰年
	public static function IsLeapYear($time = null)
	{
		return self::Datetime ( $time, 'L' ) == 1;
	}
	
	// 年
	public static function Year($time = null, $long = true)
	{
		return self::Datetime ( $time, $long ? 'Y' : 'y' );
	}
	
	// 月
	public static function Month($time = null, $long = true)
	{
		return self::Datetime ( $time, $long ? 'm' : 'n' );
	}
	// 日
	public static function Day($time = null, $long = true)
	{
		return self::Datetime ( $time, $long ? 'd' : 'j' );
	}
	
	// 小时 24小时制
	public static function Hour($time = null, $long = true)
	{
		return self::Datetime ( $time, $long ? 'H' : 'G' );
	}
	
	// 分钟
	public static function Minute($time = null)
	{
		return self::Datetime ( $time, 'i' );
	}
	
	// 秒
	public static function Second($time = null)
	{
		return self::Datetime ( $time, 's' );
	}
	
	// 辅助功能
	
	// 创建月份结构
	public static function GetChineseMonthStruct()
	{
		$rs = array ();
		for($i = 1; $i <= 12; $i ++)
		{
			$rs [$i] = array ( 'id' => $i , 'name' => self::$CHINESE_MONTY [$i] );
		}
		return $rs;
	}
	
	// 创建季度结构
	public static function GetChineseQuarterlyStruct()
	{
		$rs = array ();
		for($i = 1; $i <= 4; $i ++)
		{
			$rs [$i] = array ( 'id' => $i , 'name' => self::$CHINESE_QUARTERLY [$i] );
		}
		return $rs;
	}
	
	// 秒转换成天格式（剩余时间）
	// param: format CL:中文长格式，CS:中文短格式
	public static function Second2DayString($seconds, $format = 'CS')
	{
		$day = $hour = $minute = $second = 0;
		$flag = $seconds >= 0 ? '' : '-';
		
		$second = $seconds % 60;
		$tmp = floor ( $seconds / 60 );
		
		$minute = $tmp % 60;
		$tmp = floor ( $tmp / 60 );
		
		$hour = $tmp % 24;
		
		$day = floor ( $tmp / 24 );
		
		switch ($format)
		{
			case 'CL' :
				return sprintf ( "%d天%02d时%02d分%02d秒", $day, $hour, $minute, $second );
				break;
			
			case 'CS' :
				return sprintf ( "%d天%d时%d分%d秒", $day, $hour, $minute, $second );
				break;
			
			default :
				return sprintf ( "%dday %02d:%02d:%02d", $day, $hour, $minute, $second );
		}
	}

	public static function DateTime2Serial($datetime)
	{
		$datetime = str_replace ( '-', '', $datetime );
		$datetime = str_replace ( ':', '', $datetime );
		$datetime = str_replace ( ' ', '', $datetime );
		return $datetime;
	}

	public static function Serial2DateTime($val)
	{
		if (strlen ( $val ) != 14) return '';
		return substr ( $val, 0, 4 ) . '-' . substr ( $val, 4, 2 ) . '-' . substr ( $val, 6, 2 ) . ' ' . substr ( $val, 8, 2 ) . ':' . substr ( $val, 10, 2 ) . ':' . substr ( $val, 12, 2 );
	}
}
?>