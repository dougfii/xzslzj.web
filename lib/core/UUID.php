<?php
class UUID
{

	public static function Make()
	{
		return self::Format ( md5 ( uniqid ( rand (), true ) ) );
	}

	public static function Check($val)
	{
		return Util::IsUUID ( $val ) ? $val : null;
	}

	public static function Zero()
	{
		return self::Format ( md5 ( '0' ) );
	}

	public static function One()
	{
		return self::Format ( md5 ( '1' ) );
	}

	public static function Number($num)
	{
		return self::Format ( md5 ( '' . $num . '' ) );
	}

	public static function Format($val)
	{
		if (strlen ( $val ) != 32) return '';
		return strtolower ( substr ( $val, 0, 8 ) . '-' . substr ( $val, 8, 4 ) . '-' . substr ( $val, 12, 4 ) . '-' . substr ( $val, 16, 4 ) . '-' . substr ( $val, 20, 12 ) );
	}

	public static function ToString($val)
	{
		if (strlen ( $val ) == 36)
		{
			$val = explode ( '-', $val );
			if (count ( $val ) == 5)
			{
				$val = $val [0] . $val [1] . $val [2] . $val [3] . $val [4];
				if (strlen ( $val ) == 32) return strtolower ( $val );
			}
		}
		return '';
	}
}
?>