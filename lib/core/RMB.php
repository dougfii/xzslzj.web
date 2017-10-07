<?php
/**
 * 人民币大写转换
 *@author MOMO
 */
class RMB
{
	static $basical = array ( 0 => "零" , "壹" , "贰" , "叁" , "肆" , "伍" , "陆" , "柒" , "捌" , "玖" );
	static $advanced = array ( 1 => "拾" , "佰" , "仟" );

	public static function Number2RMB($number)
	{
		$number = trim ( $number );
		if ($number > 999999999999) throw new RMBException ( 'too big' );
		if ($number == 0) return "零";
		if (strpos ( $number, '.' ))
		{
			$number = round ( $number, 2 );
			$data = explode ( ".", $number );
			$data [0] = self::Int ( $data [0] );
			if (isset ( $data [1] ))
			{
				$data [1] = self::Dec ( $data [1] );
				return $data [0] . $data [1];
			}
			else
			{
				return $data [0] . '整';
			}
		}
		else
		{
			return self::Int ( $number ) . '整';
		}
	}

	private static function Int($number)
	{
		$arr = array_reverse ( str_split ( $number ) );
		$data = '';
		$zero = false;
		$zero_num = 0;
		foreach ( $arr as $k => $v )
		{
			$_chinese = '';
			$zero = ($v == 0) ? true : false;
			$x = $k % 4;
			if ($x && $zero && $zero_num > 1) continue;
			switch ($x)
			{
				case 0 :
					if ($zero)
					{
						$zero_num = 0;
					}
					else
					{
						$_chinese = self::$basical [$v];
						$zero_num = 1;
					}
					if ($k == 8)
					{
						$_chinese .= '亿';
					}
					elseif ($k == 4)
					{
						$_chinese .= '万';
					}
					break;
				default :
					if ($zero)
					{
						if ($zero_num == 1)
						{
							$_chinese = self::$basical [$v];
							$zero_num ++;
						}
					}
					else
					{
						$_chinese = self::$basical [$v];
						$_chinese .= self::$advanced [$x];
					}
			}
			$data = $_chinese . $data;
		}
		return $data . '元';
	}

	private static function Dec($number)
	{
		if (strlen ( $number ) < 2) $number .= '0';
		$arr = array_reverse ( str_split ( $number ) );
		$data = '';
		$zero_num = false;
		foreach ( $arr as $k => $v )
		{
			$zero = ($v == 0) ? true : false;
			$_chinese = '';
			if ($k == 0)
			{
				if (! $zero)
				{
					$_chinese = self::$basical [$v];
					$_chinese .= '分';
					$zero_num = true;
				}
			}
			else
			{
				if ($zero)
				{
					if ($zero_num)
					{
						$_chinese = self::$basical [$v];
					}
				}
				else
				{
					$_chinese = self::$basical [$v];
					$_chinese .= '角';
				}
			}
			$data = $_chinese . $data;
		}
		return $data;
	}
}
class RMBException extends Exception
{
}